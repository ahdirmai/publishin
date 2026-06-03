<?php

namespace App\Http\Controllers;

use App\Models\PostVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    /**
     * Upload a media file and attach it to a PostVersion.
     *
     * POST /api/v1/media/upload
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'file'            => [
                    'required',
                    'file',
                    'mimetypes:image/jpeg,image/png,image/gif,image/webp,video/mp4,video/quicktime',
                    'max:102400', // 100 MB in KB
                ],
                'post_version_id' => ['required', 'integer', 'exists:post_versions,id'],
            ]);

            $version = PostVersion::findOrFail($request->post_version_id);

            if ($version->post->user_id !== auth()->id()) {
                abort(403, 'You do not have permission to upload media to this post version.');
            }

            $media = $version
                ->addMediaFromRequest('file')
                ->toMediaCollection('post_media');

            return response()->json([
                'id'        => $media->id,
                'url'       => $media->getUrl(),
                'thumb'     => $media->getUrl('thumb'),
                'preview'   => $media->getUrl('preview'),
                'mime_type' => $media->mime_type,
                'size'      => $media->size,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Let Laravel's default 422 handling propagate
            throw $e;
        } catch (\Illuminate\Auth\Access\AuthorizationException|\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while uploading the file.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a media item owned by the authenticated user.
     *
     * DELETE /api/v1/media/{media}
     */
    public function destroy(Media $media): JsonResponse
    {
        try {
            /** @var PostVersion|null $version */
            $version = $media->model;

            if (! $version instanceof PostVersion) {
                abort(404, 'Media is not associated with a post version.');
            }

            if ($version->post->user_id !== auth()->id()) {
                abort(403, 'You do not have permission to delete this media.');
            }

            $media->delete();

            return response()->json(['deleted' => true]);
        } catch (\Illuminate\Auth\Access\AuthorizationException|\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting the media.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
