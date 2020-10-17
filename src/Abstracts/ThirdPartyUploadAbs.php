<?php

namespace Encore\h5upload\Abstracts;

use Encore\h5upload\models\ResourceModel;

abstract class ThirdPartyUploadAbs
{
    protected $error_message = '系统错误';


    public $config;

    public function __construct($type_dev)
    {
        $this->config = config('h5upload.' . strtolower($type_dev));
        if (method_exists($this, 'checkConfig')) {
            if (!$this->checkConfig($this->config)) {
                return false;
            }
        }
    }

    function setErrorMessage(string $message): bool
    {
        $this->error_message = $message;
        return false;
    }

    function getErrorMessage(): string
    {
        return $this->error_message;
    }

    /**
     * @inheritDoc
     */
    function getResourceUri(array $resource): array
    {
        $rus = [];
        $resourceModel = ResourceModel::whereIn('id', $resource)->get()->keyBy(function ($value, $key) {
            return $value->id;
        });
        $publicDomUrl = $this->config['public_domain'];
        if ($resourceModel) {
            foreach ($resource as $item) {
                if (isset($resourceModel[$item])) {
                    $rus[$item] = $publicDomUrl . '/' . $resourceModel[$item]['key'];
                }
            }
        }
        return $rus;
    }
}
