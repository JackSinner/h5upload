<?php

namespace Encore\h5upload;

use Encore\Admin\Show\AbstractField;
use Encore\h5upload\Interfaces\ThirdPartyUpload;
use Illuminate\Support\HtmlString;

class h5uploadShow extends AbstractField
{
    public function render()
    {
        if (is_string($this->value)) {
            $this->value = json_decode($this->value, 1);
        }
        if (is_array($this->value)) {
            $this->value = app(ThirdPartyUpload::class)->getResourceUri($this->value);
        }
        $temp = '';
        foreach ($this->value as $item) {
            $temp .= "<img style='max-width:200px;max-height:200px' class='img' src='{$item}' />";
        }
        $view = new HtmlString($temp);
        return $view;
    }
}
