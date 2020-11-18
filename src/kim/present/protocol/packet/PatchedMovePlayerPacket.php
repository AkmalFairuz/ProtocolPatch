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
use pocketmine\network\mcpe\protocol\MovePlayerPacket;

class PatchedMovePlayerPacket extends MovePlayerPacket implements PatchedPacket{
    /** @param MovePlayerPacket $from */
    public static function from(DataPacket $from) : PatchedPacket{
        $packet = new self;
        $packet->entityRuntimeId = $from->entityRuntimeId;
        $packet->position = $from->position;
        $packet->pitch = $from->pitch;
        $packet->yaw = $from->yaw;
        $packet->headYaw = $from->headYaw;
        $packet->mode = $from->mode;
        $packet->onGround = $from->onGround;
        $packet->ridingEid = $from->ridingEid;
        $packet->teleportCause = $from->teleportCause;
        $packet->teleportItem = $from->teleportItem;
        return $packet;
    }
}