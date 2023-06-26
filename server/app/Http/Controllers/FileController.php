<?php

namespace App\Http\Controllers;

use App\Models\Ad;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Checks if the user has administrator privileges.
     *
     * @return bool
     */
    public function hasAccess()
    {
        return auth()->user()->hasAdminPrivileges();
    }

    /**
     * =====================
     *     ADMIN SECTION
     * =====================
     */

     /**
     * Retrieve the files associated with a specific ad.
     *
     * @param int $id
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function fetchAsAdmin($id, Request $request)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $ad = Ad::where('id', $id)->first();

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        $files = Storage::disk('uploads')->files($ad->id);

        $fileData = [];
        foreach ($files as $file) {
            $fileName = pathinfo($file, PATHINFO_BASENAME);
            $fileUrl = Storage::disk('uploads')->url($file);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $fileType = in_array($extension, ['png', 'jpeg', 'jpg', 'webp', 'gif']) ? 'img' : 'video';

            $fileData[] = [
                'name' => $fileName,
                'url' => $fileUrl,
                'type' => $fileType,
            ];
        }

        return $this->successResponse("Files has been successfully fetched.", $fileData);
    }

    /**
     * Upload a file for a specific ad.
     *
     * @param int $id
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function uploadAsAdmin($id, Request $request)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $validatedData = $request->validate([
            'file' => 'required|file|mimes:png,jpg,jpeg,webp,mp4,gif,webm|max:10000',
        ]);

        $ad = Ad::where('id', $id)->first();

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        if (!$request->hasFile('file')) return $this->errorResponse("Uploaded file not found.", 400);

        $file = $request->file('file');

        $fileName = substr(hash('sha256', time()), 0, 16).'.'.$file->getClientOriginalExtension();
        $extension = $file->getClientOriginalExtension();
        $fileType = in_array($extension, ['png', 'jpeg', 'jpg', 'webp', 'gif']) ? 'img' : 'video';

        $path = $file->storeAs($ad->id, $fileName, ['disk' => 'uploads']);

        if ($path === false) return $this->errorResponse("An error occurred while uploading the file, try again later.", 500);

        if ($ad->file_name == null) {
            if(!$ad->update([
                'file_name' => $fileName,
                'file_type' => $fileType
            ])) return $this->errorResponse("An error occurred while updating the Ad, try again later.", 500);
        }

        $fileUrl = Storage::disk('uploads')->url($ad->id.'/'.$fileName);

        $fileData = [
            'name' => $fileName,
            'url' => $fileUrl,
            'type' => $fileType
        ];

        return $this->successResponse("File has been successfully uploaded", ['file' => $fileData, 'fileName' => $ad->file_name, 'fileType' => $ad->file_type]);
    }

    /**
     * Highlight a specific file for an ad.
     *
     * @param int $id
     * @param string $filename
     * @return \App\Http\Traits\ResponseTrait
     */
    public function highlightAsAdmin($id, $filename)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $ad = Ad::where('id', $id)->first();

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        $files = Storage::disk('uploads')->files($ad->id);

        $file = null;

        foreach($files as $element) {
            if (pathinfo($element, PATHINFO_BASENAME) == $filename) {
                $file = $element;
                break;
            };
        }

        if(!$file) return $this->errorResponse("File not found.", 404);

        $fileName = pathinfo($file, PATHINFO_BASENAME);
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $fileType = in_array($extension, ['png', 'jpeg', 'jpg', 'webp', 'gif']) ? 'img' : 'video';

        $ad->update([
            'file_name' => $fileName,
            'file_type' => $fileType
        ]);

        return $this->successResponse("File has been successfully highlighted.", ['fileName' => $ad->file_name, 'fileType' => $ad->file_type]);
    }

    /**
     * Delete a specific file associated with an ad.
     *
     * @param int $id
     * @param string $filename
     * @return \App\Http\Traits\ResponseTrait
     */
    public function deleteAsAdmin($id, $filename)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $ad = Ad::where('id', $id)->first();

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        Storage::disk('uploads')->delete($ad->id.'/'.$filename);

        if ($ad->file_name == $filename) {
            $files = Storage::disk('uploads')->files($ad->id);

            if ($files) {
                $file = $files[0];
                $fileName = pathinfo($file, PATHINFO_BASENAME);
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $fileType = in_array($extension, ['png', 'jpeg', 'jpg', 'webp', 'gif']) ? 'img' : 'video';

                $ad->update([
                    'file_name' => $fileName,
                    'file_type' => $fileType
                ]);
            }
            else {
                $ad->update([
                    'file_name' => null,
                    'file_type' => null
                ]);
            }
        }

        return $this->successResponse("File has been successfully deleted.", ['fileName' => $ad->file_name, 'fileType' => $ad->file_type]);
    }

    /**
     * =====================
     *     USER SECTION
     * =====================
     */

    /**
     * Retrieve the files associated with a specific ad.
     *
     * @param int $id
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function fetch($id, Request $request)
    {
        $ad = auth()->user()->ads()->where('id', $id)->first();

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        $files = Storage::disk('uploads')->files($ad->id);

        $fileData = [];
        foreach ($files as $file) {
            $fileName = pathinfo($file, PATHINFO_BASENAME);
            $fileUrl = Storage::disk('uploads')->url($file);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $fileType = in_array($extension, ['png', 'jpeg', 'jpg', 'webp', 'gif']) ? 'img' : 'video';

            $fileData[] = [
                'name' => $fileName,
                'url' => $fileUrl,
                'type' => $fileType,
            ];
        }

        return $this->successResponse("Files has been successfully fetched.", $fileData);
    }

    /**
     * Upload a file for a specific ad.
     *
     * @param int $id
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function upload($id, Request $request)
    {
        $validatedData = $request->validate([
            'file' => 'required|file|mimes:png,jpg,jpeg,webp,mp4,gif,webm|max:10000',
        ]);

        $ad = auth()->user()->ads()->where('id', $id)->first();

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        if (!$request->hasFile('file')) return $this->errorResponse("Uploaded file not found.", 400);

        $file = $request->file('file');

        $fileName = substr(hash('sha256', time()), 0, 16).'.'.$file->getClientOriginalExtension();
        $extension = $file->getClientOriginalExtension();
        $fileType = in_array($extension, ['png', 'jpeg', 'jpg', 'webp', 'gif']) ? 'img' : 'video';

        $path = $file->storeAs($ad->id, $fileName, ['disk' => 'uploads']);

        if ($path === false) return $this->errorResponse("An error occurred while uploading the file, try again later.", 500);

        if ($ad->file_name == null) {
            if(!$ad->update([
                'file_name' => $fileName,
                'file_type' => $fileType
            ])) return $this->errorResponse("An error occurred while updating the Ad, try again later.", 500);
        }

        $fileUrl = Storage::disk('uploads')->url($ad->id.'/'.$fileName);

        $fileData = [
            'name' => $fileName,
            'url' => $fileUrl,
            'type' => $fileType
        ];

        return $this->successResponse("File has been successfully uploaded", ['file' => $fileData, 'fileName' => $ad->file_name, 'fileType' => $ad->file_type]);
    }

    /**
     * Highlight a specific file for an ad.
     *
     * @param int $id
     * @param string $filename
     * @return \App\Http\Traits\ResponseTrait
     */
    public function highlight($id, $filename)
    {
        $ad = auth()->user()->ads()->where('id', $id)->first();

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        $files = Storage::disk('uploads')->files($ad->id);

        $file = null;

        foreach($files as $element) {
            if (pathinfo($element, PATHINFO_BASENAME) == $filename) {
                $file = $element;
                break;
            };
        }

        if(!$file) return $this->errorResponse("File not found.", 404);

        $fileName = pathinfo($file, PATHINFO_BASENAME);
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $fileType = in_array($extension, ['png', 'jpeg', 'jpg', 'webp', 'gif']) ? 'img' : 'video';

        $ad->update([
            'file_name' => $fileName,
            'file_type' => $fileType
        ]);

        return $this->successResponse("File has been successfully highlighted.", ['fileName' => $ad->file_name, 'fileType' => $ad->file_type]);
    }

    /**
     * Delete a specific file associated with an ad.
     *
     * @param int $id
     * @param string $filename
     * @return \App\Http\Traits\ResponseTrait
     */
    public function delete($id, $filename)
    {
        $ad = auth()->user()->ads()->where('id', $id)->first();

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        Storage::disk('uploads')->delete($ad->id.'/'.$filename);

        if ($ad->file_name == $filename) {
            $files = Storage::disk('uploads')->files($ad->id);

            if ($files) {
                $file = $files[0];
                $fileName = pathinfo($file, PATHINFO_BASENAME);
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $fileType = in_array($extension, ['png', 'jpeg', 'jpg', 'webp', 'gif']) ? 'img' : 'video';

                $ad->update([
                    'file_name' => $fileName,
                    'file_type' => $fileType
                ]);
            }
            else {
                $ad->update([
                    'file_name' => null,
                    'file_type' => null
                ]);
            }
        }

        return $this->successResponse("File has been successfully deleted.", ['fileName' => $ad->file_name, 'fileType' => $ad->file_type]);
    }
}
