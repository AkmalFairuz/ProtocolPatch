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

namespace kim\present\protocol\packet;

use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;

class PatchedStartGamePacketPacket extends StartGamePacket implements PatchedPacket{
    /** @param StartGamePacket $from */
    public static function from(DataPacket $from) : PatchedPacket{
        $packet = new self;
        $packet->entityUniqueId = $from->entityUniqueId;
        $packet->entityRuntimeId = $from->entityRuntimeId;
        $packet->playerGamemode = $from->playerGamemode;
        $packet->playerPosition = $from->playerPosition;
        $packet->pitch = $from->pitch;
        $packet->yaw = $from->yaw;
        $packet->seed = $from->seed;
        $packet->spawnSettings = $from->spawnSettings;
        $packet->generator = $from->generator;
        $packet->worldGamemode = $from->worldGamemode;
        $packet->difficulty = $from->difficulty;
        $packet->spawnX = $from->spawnX;
        $packet->spawnY = $from->spawnY;
        $packet->spawnZ = $from->spawnZ;
        $packet->hasAchievementsDisabled = $from->hasAchievementsDisabled;
        $packet->time = $from->time;
        $packet->eduEditionOffer = $from->eduEditionOffer;
        $packet->hasEduFeaturesEnabled = $from->hasEduFeaturesEnabled;
        $packet->eduProductUUID = $from->eduProductUUID;
        $packet->rainLevel = $from->rainLevel;
        $packet->lightningLevel = $from->lightningLevel;
        $packet->hasConfirmedPlatformLockedContent = $from->hasConfirmedPlatformLockedContent;
        $packet->isMultiplayerGame = $from->isMultiplayerGame;
        $packet->hasLANBroadcast = $from->hasLANBroadcast;
        $packet->xboxLiveBroadcastMode = $from->xboxLiveBroadcastMode;
        $packet->platformBroadcastMode = $from->platformBroadcastMode;
        $packet->commandsEnabled = $from->commandsEnabled;
        $packet->isTexturePacksRequired = $from->isTexturePacksRequired;
        $packet->gameRules = $from->gameRules;
        $packet->hasBonusChestEnabled = $from->hasBonusChestEnabled;
        $packet->hasStartWithMapEnabled = $from->hasStartWithMapEnabled;
        $packet->defaultPlayerPermission = $from->defaultPlayerPermission;
        $packet->serverChunkTickRadius = $from->serverChunkTickRadius;
        $packet->hasLockedBehaviorPack = $from->hasLockedBehaviorPack;
        $packet->hasLockedResourcePack = $from->hasLockedResourcePack;
        $packet->isFromLockedWorldTemplate = $from->isFromLockedWorldTemplate;
        $packet->useMsaGamertagsOnly = $from->useMsaGamertagsOnly;
        $packet->isFromWorldTemplate = $from->isFromWorldTemplate;
        $packet->isWorldTemplateOptionLocked = $from->isWorldTemplateOptionLocked;
        $packet->onlySpawnV1Villagers = $from->onlySpawnV1Villagers;
        $packet->vanillaVersion = $from->vanillaVersion;
        $packet->limitedWorldWidth = $from->limitedWorldWidth;
        $packet->limitedWorldLength = $from->limitedWorldLength;
        $packet->isNewNether = $from->isNewNether;
        $packet->experimentalGameplayOverride = $from->experimentalGameplayOverride;
        $packet->levelId = $from->levelId;
        $packet->worldName = $from->worldName;
        $packet->premiumWorldTemplateId = $from->premiumWorldTemplateId;
        $packet->isTrial = $from->isTrial;
        $packet->isMovementServerAuthoritative = $from->isMovementServerAuthoritative;
        $packet->currentTick = $from->currentTick;
        $packet->enchantmentSeed = $from->enchantmentSeed;
        $packet->multiplayerCorrelationId = $from->multiplayerCorrelationId;
        $packet->blockTable = $from->blockTable;
        $packet->itemTable = $from->itemTable;
        $packet->enableNewInventorySystem = $from->enableNewInventorySystem;
        return $packet;
    }
}