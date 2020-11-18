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

namespace kim\present\protocol\patch;

trait MoveActorDeltaPacketPatch{
    public function decodePayload(){
        $this->putEntityRuntimeId($this->entityRuntimeId);
        $this->putLShort($this->flags);
        $this->xDiff = $this->readCoord(self::FLAG_HAS_X); // fixed: Change type to float from int
        $this->yDiff = $this->readCoord(self::FLAG_HAS_Y); // fixed: Change type to float from int
        $this->zDiff = $this->readCoord(self::FLAG_HAS_Z); // fixed: Change type to float from int
        $this->xRot = $this->readRotation(self::FLAG_HAS_ROT_X);
        $this->yRot = $this->readRotation(self::FLAG_HAS_ROT_Y);
        $this->zRot = $this->readRotation(self::FLAG_HAS_ROT_Z);
    }

    public function readCoord(int $flag) : float{
        if(($this->flags & $flag) !== 0){
            return $this->getLFloat();
        }
        return 0.0;
    }

    public function readRotation(int $flag) : float{
        if(($this->flags & $flag) !== 0){
            return $this->getByteRotation();
        }
        return 0.0;
    }

    protected function encodePayload(){
        $this->putEntityRuntimeId($this->entityRuntimeId);
        $this->putLShort($this->flags);
        $this->writeCoord(self::FLAG_HAS_X, $this->xDiff); // fixed: Change type to float from int
        $this->writeCoord(self::FLAG_HAS_Y, $this->yDiff); // fixed: Change type to float from int
        $this->writeCoord(self::FLAG_HAS_Z, $this->zDiff); // fixed: Change type to float from int
        $this->writeRotation(self::FLAG_HAS_ROT_X, $this->xRot);
        $this->writeRotation(self::FLAG_HAS_ROT_Y, $this->yRot);
        $this->writeRotation(self::FLAG_HAS_ROT_Z, $this->zRot);
    }

    private function writeCoord(int $flag, int $val) : void{
        if(($this->flags & $flag) !== 0){
            $this->putLFloat((float) $val);
        }
    }

    private function writeRotation(int $flag, float $val) : void{
        if(($this->flags & $flag) !== 0){
            $this->putByteRotation($val);
        }
    }
}