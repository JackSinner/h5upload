<?php

namespace Encore\h5upload;

use Encore\Admin\Grid\Displayers\AbstractDisplayer;
use Encore\h5upload\Interfaces\ThirdPartyUpload;

class h5uploadColumn extends AbstractDisplayer
{

    public function display()
    {
        $rus = [];
        if (is_string($this->value)) {
            $this->value = json_decode($this->value, 1);
        }
        if (is_array($this->value)) {
            $rus = app(ThirdPartyUpload::class)->getResourceUri($this->value);
        }
        if (!empty($rus)) {
            foreach ($rus as $key => &$item) {
                $item = "<a href='" . $item . "' target='_blank'>{$item}</a>";
            }
        }
        return $rus;
    }
}
