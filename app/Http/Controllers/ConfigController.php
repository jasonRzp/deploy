<?php

namespace App\Http\Controllers;

use App\Services\UtilsService;
use http\Env\Response;
use Illuminate\Http\Request;
use Log;

class ConfigController extends Controller
{
    /**
     * 验证该路径是否存在
     *
     * @param Request $request
     * @return bool
     */
    public function validDir(Request $request)
    {
        $data = [
            'code' => 4000,
            'msg' => 'Wrong dir params, please check it.',
        ];
        $dir = $request->get('dir');
        Log::info('Enter the dir is '.$dir);

        if (!isset($dir) || empty($dir))
            return response()->json($data);
        Log::info('php '.substr($dir, -strlen('.php')));

        if ((substr($dir, -strlen('.php')) != '.php')
            && (substr($dir, -strlen('.ini')) != '.ini')
            && (substr($dir, -strlen('.yml')) != '.yml')
            && (substr($dir, -strlen('.conf')) != '.conf')  ) {
            $data['code'] = 4004;
            $data['msg'] = '请输入有效路径';
            return response()->json($data);
        }

        Log::info('dirname '.dirname($dir));
        if (strpos($dir, DIRECTORY_SEPARATOR) !== false) {
            if (!file_exists(dirname($dir))) {
                $data['code'] = 4004;
                $data['msg'] = '请输入有效路径';
                return response()->json($data);
            }
        } else {
            $fileDir = config_path(). '/' . $dir;
            Log::info('the config dir '.$fileDir);
            if (file_exists($fileDir)) {
                $data['code'] = 2001;
                $data['msg'] = '该配置文件已存在，确定要覆盖此文件内容吗？';
                return response()->json($data);
            }
        }

        $data['code'] = 2000;
        $data['msg'] = '配置文件验证通过';
        return response()->json($data);
    }

    public function createFile(Request $request)
    {
        $data = [
            'code' => 4000,
            'msg' => 'Wrong params, check it.',
        ];

        $dir = $request->get('dir');
        $content = $request->get('content');
        if (!isset($dir)
            || empty($dir)
            || !isset($content)
            || empty($content)) {
            return response()->json($data);
        }


        Log::info('dirname '.dirname($dir));
        if (strpos($dir, DIRECTORY_SEPARATOR) === false) {
            $dir = config_path(). '/' . $dir;
        }

        $ret = UtilsService::saveConfig($dir, $content);
        if ($ret === false) {
            Log::info('create or update config file is failed, check it!');
            $data['code']= 5000;
            $data['msg'] = '创建配置文件失败';
            return response()->json($data);
        }

        $data['code']= 2000;
        $data['msg'] = '成功创建配置文件';
        return response()->json($data);
    }
}
