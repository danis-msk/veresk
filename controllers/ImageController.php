<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use Imagine\Imagick\Imagine;

class ImageController extends Controller
{
    public function actionResizeAndWatermark(): string {
        if (Yii::$app->request->isAjax) {
            $originalPath = Yii::getAlias('@app/downloads/original.gif');
            $watermarkPath = Yii::getAlias('@app/downloads/watermark.png');
            $resultPath = Yii::getAlias('@app/downloads/watermarked/original-watermarked.gif');
            $imagine = new Imagine();
            $originalImage = $imagine->open($originalPath);
            // получить размеры
            $originalSize = $originalImage->getSize();
            $originalWidth = $originalSize->getWidth();
            $originalHeight = $originalSize->getHeight();
            $newWidth = Yii::$app->request->post('width');
            $newHeight = Yii::$app->request->post('height');
    
            // если одна из сторон не задана, вычислить ее с сохранением пропорций
            if (empty($newWidth) && !empty($newHeight)) {
                $newWidth = round($newHeight * ($originalWidth / $originalHeight));
            } elseif (!empty($newWidth) && empty($newHeight)) {
                $newHeight = round($newWidth * ($originalHeight / $originalWidth));
            }
    
            $imagick = $originalImage->getImagick();
            $imagick = $this->applyWatermark($imagick, $watermarkPath, $newWidth, $newHeight);
            $imagick->writeImages($resultPath, true);

            $debugInfo = $this->getDegubInfo($originalWidth, $originalHeight, $newWidth, $newHeight);
    
            return $debugInfo;
        }
    
        return $this->render('resize-and-watermark');
    }

    private function getDegubInfo(int $originalWidth, int $originalHeight, int $newWidth, int $newHeight): string {
        $debugInfo = '<pre>' . PHP_EOL;
        $debugInfo .= 'Начальная ширина: ' . $originalWidth . PHP_EOL;
        $debugInfo .= 'Начальная высота: ' . $originalHeight . PHP_EOL;
        $debugInfo .= 'Новая ширина: ' . $newWidth . PHP_EOL;
        $debugInfo .= 'Новая высота: ' . $newHeight . PHP_EOL;
        $debugInfo .= '</pre>';
        return $debugInfo;
    }
    
    private function applyWatermark(\Imagick $imagick, string $watermarkPath, int $newWidth, int $newHeight): \Imagick {
        $watermark = new \Imagick($watermarkPath);
        $watermarkWidth = $watermark->getImageWidth();
        $watermarkHeight = $watermark->getImageHeight();

        // если ватермарк больше по размеру
        if ($watermarkWidth > $newWidth || $watermarkHeight > $newHeight) {
            // вычислить коэффициент масштабирования по ширине и высоте
            $widthRatio = $newWidth / $watermarkWidth;
            $heightRatio = $newHeight / $watermarkHeight;
            // получить наименьший коэффициент для масштабирования
            $scaleRatio = min($widthRatio, $heightRatio);
            
            $watermarkWidth = round($watermarkWidth * $scaleRatio);
            $watermarkHeight = round($watermarkHeight * $scaleRatio);
        }

        $positionX = round(($newWidth - $watermarkWidth) / 2);
        $positionY = round(($newHeight - $watermarkHeight) / 2);
        
        foreach ($imagick as $frame) {
            $frame->resizeImage($newWidth, $newHeight, $watermark::FILTER_LANCZOS, 1);
            $watermark->resizeImage($watermarkWidth, $watermarkHeight, $watermark::FILTER_LANCZOS, 1);
            $frame->compositeImage($watermark, $watermark::COMPOSITE_OVER, $positionX, $positionY);
        }
        
        return $imagick;
    }
}
