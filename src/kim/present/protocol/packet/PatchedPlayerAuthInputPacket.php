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

use kim\present\protocol\patch\PlayerAuthInputPacketPatch;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;

class PatchedPlayerAuthInputPacket extends PlayerAuthInputPacket implements PatchedPacket{
    use PlayerAuthInputPacketPatch;

    /** @param PlayerAuthInputPacket $from */
    public static function from(DataPacket $from) : PatchedPacket{
        $packet = new self;
        $packet->position = $from->getPosition();
        $packet->pitch = $from->getPitch();
        $packet->yaw = $from->getYaw();
        $packet->headYaw = $from->getHeadYaw();
        $packet->moveVecX = $from->getMoveVecX();
        $packet->moveVecZ = $from->getMoveVecZ();
        $packet->inputFlags = $from->getInputFlags();
        $packet->inputMode = $from->getInputMode();
        $packet->playMode = $from->getPlayMode();
        $packet->vrGazeDirection = $from->getVrGazeDirection();
        return $packet;
    }
}