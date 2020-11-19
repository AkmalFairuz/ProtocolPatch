<?php

/*
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  PresentKim (debe3721@gmail.com)
 * @link    https://github.com/PresentKim
 * @license https://www.gnu.org/licenses/gpl-3.0 GPL-3.0 License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

declare(strict_types=1);

namespace kim\present\protocol;

use kim\present\protocol\packet\PatchedContainerClosePacket;
use kim\present\protocol\packet\PatchedMoveActorDeltaPacket;
use kim\present\protocol\packet\PatchedMovePlayerPacket;
use kim\present\protocol\packet\PatchedPacket;
use kim\present\protocol\packet\PatchedPlayerAuthInputPacket;
use kim\present\protocol\packet\PatchedPlayerListPacket;
use kim\present\protocol\packet\PatchedPlayerSkinPacket;
use kim\present\protocol\packet\PatchedResourcePackStackPacket;
use kim\present\protocol\packet\PatchedSetActorDataPacket;
use kim\present\protocol\packet\PatchedStartGamePacketPacket;
use kim\present\protocol\packet\PatchedUpdateAttributesPacket;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\nbt\LittleEndianNBTStream;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\NetworkBinaryStream;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\PacketPool;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class ProtocolPatch extends PluginBase implements Listener{
    public const PATHED_PACKETS = [
        ProtocolInfo::CONTAINER_CLOSE_PACKET => PatchedContainerClosePacket::class,
        ProtocolInfo::MOVE_ACTOR_DELTA_PACKET => PatchedMoveActorDeltaPacket::class,
        ProtocolInfo::MOVE_PLAYER_PACKET => PatchedMovePlayerPacket::class,
        ProtocolInfo::RESOURCE_PACK_STACK_PACKET => PatchedResourcePackStackPacket::class,
        ProtocolInfo::SET_ACTOR_DATA_PACKET => PatchedSetActorDataPacket::class,
        ProtocolInfo::START_GAME_PACKET => PatchedStartGamePacketPacket::class,
        ProtocolInfo::UPDATE_ATTRIBUTES_PACKET => PatchedUpdateAttributesPacket::class,
        ProtocolInfo::PLAYER_LIST_PACKET => PatchedPlayerListPacket::class,
        ProtocolInfo::PLAYER_SKIN_PACKET => PatchedPlayerSkinPacket::class,
        ProtocolInfo::PLAYER_AUTH_INPUT_PACKET => PatchedPlayerAuthInputPacket::class,
    ];

    /** @var string|null */
    public static $itemTableCache = null;

    /** @var bool */
    private $ignore = false;

    public function onLoad(){
        foreach(self::PATHED_PACKETS as $pid => $packetClass){
            PacketPool::registerPacket(new $packetClass());
        }

        //Update runtime tables, and remove shuffling
        $this->updateRuntimeBlockTable();
        $this->updateRuntimeItemTable();
    }

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    /** @priority HIGHEST */
    public function onDataPacketReceiveEvent(DataPacketReceiveEvent $event) : void{
        $packet = $event->getPacket();
        if($packet instanceof LoginPacket){
            if($packet->protocol === 419){
                $packet->protocol = ProtocolInfo::CURRENT_PROTOCOL;
            }else{
                $packet->protocol = ProtocolInfo::CURRENT_PROTOCOL - 1; //for Prevent other protocol
            }
        }elseif(!$packet instanceof BatchPacket){
            if($packet instanceof PlayerAuthInputPacket){
                $event->setCancelled(true);
                return;
            }
        }
    }

    /** @priority HIGHEST */
    public function onDataPacketSendEvent(DataPacketSendEvent $event) : void{
        if($this->ignore)
            return;

        $packet = $event->getPacket();
        $player = $event->getPlayer();
        if($packet instanceof BatchPacket){
            $packets = [];
            $resolved = false;
            foreach($packet->getPackets() as $key => $buf){
                $pk = PacketPool::getPacket($buf);
                $pk->decode();
                if($this->resolveSend($pk, $player)){
                    $resolved = true;
                }else{
                    $packets[] = $pk;
                }
            }
            if($resolved){
                if(count($packets) > 0){
                    $packet->payload = "";
                    foreach($packets as $pk){
                        $packet->addPacket($pk);
                    }
                    $packet->encode();
                }else{
                    $event->setCancelled(true);
                }
            }
        }elseif($this->resolveSend($packet, $player)){
            $event->setCancelled(true);
        }
    }

    public function resolveSend(DataPacket $packet, Player $player) : bool{
        if($packet instanceof PatchedPacket || !isset(self::PATHED_PACKETS[$pid = $packet->pid()]))
            return false;

        /** @phpstan-param class-string<PathedPacket> $packetClass */
        $packetClass = self::PATHED_PACKETS[$pid];

        $this->ignore = true;
        /** @noinspection PhpUndefinedMethodInspection */
        $player->sendDataPacket($packetClass::from($packet));
        $this->ignore = false;
        return true;
    }

    /** @throws \ReflectionException */
    public function updateRuntimeBlockTable() : void{
        /** @var CompoundTag[]| $table */
        $table = (new LittleEndianNBTStream())->read(stream_get_contents($this->getResource("runtime_block_states.dat")));
        if(!($table instanceof ListTag) or $table->getTagType() !== NBT::TAG_Compound){ //this is a little redundant currently, but good for auto complete and makes phpstan happy
            throw new \RuntimeException("Invalid blockstates table, expected TAG_List<TAG_Compound> root");
        }
        $list = $table->getValue();
        $reflectionClass = new \ReflectionClass(RuntimeBlockMapping::class);
        /** @see RuntimeBlockMapping::$bedrockKnownStates */
        $bedrockKnownStatesProp = $reflectionClass->getProperty("bedrockKnownStates");
        $bedrockKnownStatesProp->setAccessible(true);
        $bedrockKnownStatesProp->setValue(null, $list);

        /** @see RuntimeBlockMapping::setupLegacyMappings() */
        $setupLegacyMappingsMeth = $reflectionClass->getMethod("setupLegacyMappings");
        $setupLegacyMappingsMeth->setAccessible(true);
        $setupLegacyMappingsMeth->invoke(null);
    }

    public function updateRuntimeItemTable() : void{
        /** @var int[][]|string[][] $table */
        $table = json_decode(stream_get_contents($this->getResource("runtime_item_ids.json")), true);
        $stream = new NetworkBinaryStream();
        $stream->putUnsignedVarInt(count($table));
        foreach($table as $item){
            $stream->putString($item["name"]);
            $stream->putLShort($item["id"]);
            $stream->putBool(false); // added: Component item
        }
        self::$itemTableCache = $stream->getBuffer();
    }
}