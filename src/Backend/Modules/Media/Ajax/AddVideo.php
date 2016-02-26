<?php

namespace Backend\Modules\Media\Ajax;

use Backend\Core\Engine\Base\AjaxAction;
use Backend\Core\Engine\Form AS BackendForm;
use Backend\Modules\Media\Engine\Helper as BackendMediaHelper;
use Frontend\Core\Engine\Language AS FrontendLanguage;
use Backend\Core\Engine\Template;
use Backend\Core\Engine\Model as BackendModel;

/**
 * Add link mediaitem to item
 *
 * @author Nick Vandevenne<nick@comsa.be>
 */
class AddVideo extends AjaxAction
{
    /**
     * Execute the action
     */
    private function getVimeoId($url)
    {
        if (preg_match('#(?:https?://)?(?:www.)?(?:player.)?vimeo.com/(?:[a-z]*/)*([0-9]{6,11})[?]?.*#', $url, $m)) {
            return $m[1];
        }
        return false;
    }

    private function getYoutubeId($url)
    {
        $parts = parse_url($url);
        if (isset($parts['host'])) {
            $host = $parts['host'];
            if (
                false === strpos($host, 'youtube') &&
                false === strpos($host, 'youtu.be')
            ) {
                return false;
            }
        }
        if (isset($parts['query'])) {
            parse_str($parts['query'], $qs);
            if (isset($qs['v'])) {
                return $qs['v'];
            } else if (isset($qs['vi'])) {
                return $qs['vi'];
            }
        }
        if (isset($parts['path'])) {
            $path = explode('/', trim($parts['path'], '/'));
            return $path[count($path) - 1];
        }
        return false;
    }

    public function execute()
    {
        parent::execute();

        //--Get the video info
        //$video_type = \SpoonFilter::getPostValue('video_type', null, '', 'int');
        $video_url = \SpoonFilter::getPostValue('video', null, '', 'string');
        if (preg_match('%youtube|youtu\.be%i', $video_url)) {
            $video_type = 0;
            $video_id = self::getYoutubeId($video_url);
        } elseif (preg_match('%vimeo%i', $video_url)) {
            $video_type = 1;
            $video_id = self::getVimeoId($video_url);
        } elseif (preg_match('%vine%i', $video_url)) {
            $video_type = 2;
            $video_id = preg_replace('/^.*\//', '', $video_url);
        }
        if (isset($video_id)) {
            //--Set module
            $module = (string)\SpoonFilter::getPostValue('mediaModule', null, '', 'string');
            //--Set action
            $action = (string)\SpoonFilter::getPostValue('mediaAction', null, '', 'string');
            //--Set the id
            $id = (int)\SpoonFilter::getPostValue('mediaId', null, '', 'int');
            //--Set the type
            $type = (string)\SpoonFilter::getPostValue('mediaType', null, '', 'string');
            //--Create media object
            $media = new BackendMediaHelper(new BackendForm('add_image', null, 'post', false), $module, $id, $action, $type);
            //--Validate media -> add video
            $media->addVideo($video_type, $video_id);

            $tpl = new Template();

            $media->item['txtText'] = $media->frm->addTextarea("text-" . $media->item["id"], $media->item['text'])->setAttribute('style', 'resize: none;')->parse();
            switch($media->item['extension']){
                //youtube
                case 0:
                    $media->item['video_html'] = '<iframe id="ytplayer" type="text/html" width="100%" src="http://www.youtube.com/embed/' . $media->item['filename'] . '?autoplay=0" frameborder="0"></iframe>';
                    break;
                //vimeo
                case 1:
                    $media->item['video_html'] = '<iframe src="//player.vimeo.com/video/'. $media->item['filename'] .'" width="100%" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
                    break;
                //vine
                case 2:
                    $media->item['video_html'] = '<iframe src="https://vine.co/v/'. $media->item['filename'] .'/embed/postcard" width="100%" frameborder="0"></iframe><script src="https://platform.vine.co/static/scripts/embed.js"></script>';
                    break;
                default:
                    $media->item['video_html'] = "";
                    break;
            }
            $tpl->assign('mediaItems', array('videos' => array($media->item)));

            $html = $tpl->getContent(BACKEND_MODULES_PATH . '/Media/Layout/Templates/Ajax/Video.tpl');

            $this->output(self::OK, array($media->item['filetype'], $html), FrontendLanguage::msg('Success'));
        } else {
            $this->output(self::OK, null, 'video not added');
        }
        // success output
    }
}