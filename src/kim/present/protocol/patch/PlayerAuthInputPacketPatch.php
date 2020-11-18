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

use pocketmine\math\Vector3;

trait PlayerAuthInputPacketPatch{
    /** @var Vector3 */
    private $position;
    /** @var float */
    private $pitch;
    /** @var float */
    private $yaw;
    /** @var float */
    private $headYaw;
    /** @var float */
    private $moveVecX;
    /** @var float */
    private $moveVecZ;
    /** @var int */
    private $inputFlags;
    /** @var int */
    private $inputMode;
    /** @var int */
    private $playMode;
    /** @var Vector3|null */
    private $vrGazeDirection = null;
    /** @var int */
    private $tick;
    /** @var Vector3|null */
    private $delta = null;

    protected function decodePayload() : void{
        parent::decodePayload();
        $this->tick = $this->getUnsignedVarLong();
        $this->delta = $this->getVector3();
    }

    protected function encodePayload() : void{
        parent::encodePayload();
        $this->putUnsignedVarLong($this->tick);
        $this->putVector3($this->delta);
    }

    public function getTick() : int{
        return $this->tick;
    }

    public function getDelta() : Vector3{
        return $this->delta;
    }
}