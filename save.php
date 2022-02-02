<?php

namespace App\Service;

use App\Entity\Content;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Media\Frame;
use FFMpeg\FFMpeg;

class imageExtractor
{
    public function imageExtraction(Frame $frame, Content $content)
    {
        $ffmpeg = FFMpeg\FFMpeg::create();
        $video = $fm->open('../sources/content_videos/video1-61fab3503aa16.mp4/');


        $frame = $content->getUrl()->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(42));
        $frame->save('image.jpg');
    }
}