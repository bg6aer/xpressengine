<?php
namespace App\Http\Controllers;

use Xpressengine\Http\Request;
use Xpressengine\Support\Exceptions\FileAccessDeniedHttpException;
use Xpressengine\Support\Exceptions\HttpXpressengineException;
use Xpressengine\Support\Exceptions\InvalidArgumentHttpException;
use Xpressengine\Theme\ThemeEntity;

class ThemeController extends Controller
{

    public function getEdit(Request $request)
    {
        $themeId = $request->get('theme');
        $fileName = $request->get('file');
        $type = $request->get('type', 'template');

        // TODO: validate themeid, fileName
        if($themeId === null) {
            $e = new InvalidArgumentHttpException();
            $e->setMessage('잘못된 요청입니다.');
            throw $e;
        }

        $theme = \Theme::getTheme($themeId);

        /** @var ThemeEntity $theme */
        $files = $theme->getEditFiles();

        if ($fileName === null) {
            $fileName = key($files[$type]);
        }

        if (!is_writable($files[$type][$fileName])) {
            \Session::flash('alert', ['type' => 'danger', 'message' => '파일을 수정할 권한이 없습니다.']);
        }

        $fileContent = file_get_contents($files[$type][$fileName]);
        $editFile = [
            'fileName' => $fileName,
            'path' => $files[$type][$fileName],
            'content' => $fileContent
        ];

        return \Presenter::make(
            'theme.edit',
            [
                'theme' => $theme,
                'files' => $files,
                'editFile' => $editFile,
                'type' => $type
            ]
        );
    }

    public function postEdit()
    {
        $themeId = \Input::get('theme');
        $fileName = \Input::get('file');
        $type = \Input::get('type', 'template');

        $content = \Input::get('content');

        $theme = \Theme::getTheme($themeId);
        $files = $theme->getEditFiles();

        $filePath = $files[$type][$fileName];

        try {
            file_put_contents($filePath, $content);
        } catch (\Exception $e) {
            throw new FileAccessDeniedHttpException();
        }

        return \Redirect::back()->with('alert', ['type' => 'success', 'message' => '저장되었습니다.']);
    }
}

