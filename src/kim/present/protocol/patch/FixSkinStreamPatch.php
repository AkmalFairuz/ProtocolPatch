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

use pocketmine\network\mcpe\protocol\types\PersonaPieceTintColor;
use pocketmine\network\mcpe\protocol\types\PersonaSkinPiece;
use pocketmine\network\mcpe\protocol\types\SkinAnimation;
use pocketmine\network\mcpe\protocol\types\SkinData;
use pocketmine\network\mcpe\protocol\types\SkinImage;

trait FixSkinStreamPatch{
    private function getImage() : SkinImage{
        $width = $this->getLInt();
        $height = $this->getLInt();
        $data = $this->getString();
        return new SkinImage($height, $width, $data);
    }

    public function getSkin() : SkinData{
        $skinId = $this->getString();
        $skinResourcePatch = $this->getString();
        $skinData = $this->getImage();
        $animationCount = $this->getLInt();
        $animations = [];
        for($i = 0; $i < $animationCount; ++$i){
            $skinImage = $this->getImage();
            $animationType = $this->getLInt();
            $animationFrames = $this->getLFloat();
            $this->getLInt(); // added: AnimationExpression
            $animations[] = new SkinAnimation($skinImage, $animationType, $animationFrames);
        }
        $capeData = $this->getImage();
        $geometryData = $this->getString();
        $animationData = $this->getString();
        $premium = $this->getBool();
        $persona = $this->getBool();
        $capeOnClassic = $this->getBool();
        $capeId = $this->getString();
        $fullSkinId = $this->getString();
        $armSize = $this->getString();
        $skinColor = $this->getString();
        $personaPieceCount = $this->getLInt();
        $personaPieces = [];
        for($i = 0; $i < $personaPieceCount; ++$i){
            $pieceId = $this->getString();
            $pieceType = $this->getString();
            $packId = $this->getString();
            $isDefaultPiece = $this->getBool();
            $productId = $this->getString();
            $personaPieces[] = new PersonaSkinPiece($pieceId, $pieceType, $packId, $isDefaultPiece, $productId);
        }
        $pieceTintColorCount = $this->getLInt();
        $pieceTintColors = [];
        for($i = 0; $i < $pieceTintColorCount; ++$i){
            $pieceType = $this->getString();
            $colorCount = $this->getLInt();
            $colors = [];
            for($j = 0; $j < $colorCount; ++$j){
                $colors[] = $this->getString();
            }
            $pieceTintColors[] = new PersonaPieceTintColor(
                $pieceType,
                $colors
            );
        }

        return new SkinData($skinId, $skinResourcePatch, $skinData, $animations, $capeData, $geometryData, $animationData, $premium, $persona, $capeOnClassic, $capeId, $fullSkinId, $armSize, $skinColor, $personaPieces, $pieceTintColors);
    }

    public function putImage(SkinImage $image) : void{
        $this->putLInt($image->getWidth());
        $this->putLInt($image->getHeight());
        $this->putString($image->getData());
    }

    public function putSkin(SkinData $skin){
        $this->putString($skin->getSkinId());
        $this->putString($skin->getResourcePatch());
        $this->putImage($skin->getSkinImage());
        $this->putLInt(count($skin->getAnimations()));
        foreach($skin->getAnimations() as $animation){
            $this->putImage($animation->getImage());
            $this->putLInt($animation->getType());
            $this->putLFloat($animation->getFrames());
            $this->putLInt(0); // added: AnimationExpression
        }
        $this->putImage($skin->getCapeImage());
        $this->putString($skin->getGeometryData());
        $this->putString($skin->getAnimationData());
        $this->putBool($skin->isPremium());
        $this->putBool($skin->isPersona());
        $this->putBool($skin->isPersonaCapeOnClassic());
        $this->putString($skin->getCapeId());
        $this->putString($skin->getFullSkinId());
        $this->putString($skin->getArmSize());
        $this->putString($skin->getSkinColor());
        $this->putLInt(count($skin->getPersonaPieces()));
        foreach($skin->getPersonaPieces() as $piece){
            $this->putString($piece->getPieceId());
            $this->putString($piece->getPieceType());
            $this->putString($piece->getPackId());
            $this->putBool($piece->isDefaultPiece());
            $this->putString($piece->getProductId());
        }
        $this->putLInt(count($skin->getPieceTintColors()));
        foreach($skin->getPieceTintColors() as $tint){
            $this->putString($tint->getPieceType());
            $this->putLInt(count($tint->getColors()));
            foreach($tint->getColors() as $color){
                $this->putString($color);
            }
        }
    }
}