<?php

namespace App\Http\Controllers;

use App\Helpers\FileHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Ddeboer\Imap\Exception\Exception;
use Ddeboer\Imap\Server;
use Ddeboer\Imap\Message\Attachment;
use Ddeboer\Imap\Exception\AuthenticationFailedException;
use Ddeboer\Imap\Exception\MailboxDoesNotExistException;
use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;
use League\Flysystem\Filesystem;

class HomeController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if (Storage::disk('local')->exists('config.json') && !session()->has('access_token')) {
            $redirectUri = route('home.index');
            try {
                $client = new \Google_Client();
                $client->setAuthConfig(storage_path() . '/app/config.json');
                $client->setRedirectUri($redirectUri);
                $client->addScope(\Google_Service_Drive::DRIVE);
                $authUrl = $client->createAuthUrl();

                if (($code = $request->get('code'))) {
                    $token = $client->fetchAccessTokenWithAuthCode($code);
                    $client->setAccessToken($token);
                    session(['access_token' => $token]);
                    return redirect()->route('home.index');
                } else {
                    return redirect()->to($authUrl);
                }
            }
            catch (\Exception $e) {
                $errorMessage = 'An invalid exception was thrown. Message: ' . $e->getMessage();
                Storage::disk('local')->delete('config.json');
                $request->session()->forget('access_token');
                $request->session()->flash('message', $errorMessage);
                return redirect()->route('home.index');
            }
            catch (\Google_Exception $e) {
                $errorMessage = 'Google_Exception: ' . $e->getMessage();
                $request->session()->flash('message', $errorMessage);
                return redirect()->route('home.index');
            }
        } else {
            return view('pages.index', [
                'configExists' => (Storage::disk('local')->exists('config.json') && session()->has('access_token')),
            ]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadConfig(Request $request)
    {
        if (($config = $request->file('config')) && $config->getClientMimeType() == 'application/json') {
            if ($config->storeAs('/', 'config.json')) {
                $request->session()->flash('message', 'Configuration has been uploaded successfully.');
            } else {
                $request->session()->flash('message', 'Error occurred on file upload.');
            }
        } else {
            $request->session()->flash('message', 'Please upload file with correct extension.');
        }

        return redirect()->back();
    }

    /**
     * Upload pdfs from Gmail account to Google Drive.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function uploadFromMail(Request $request)
    {
        if (($access_token = session('access_token'))) {
            try {
                $client = new \Google_Client();
                $client->setAuthConfig(storage_path() . '/app/config.json');
                $client->addScope(\Google_Service_Drive::DRIVE);
                $client->setAccessToken($access_token);
                if ($client->isAccessTokenExpired()) {
                    $request->session()->forget('access_token');
                    return redirect()->route('home.index');
                }
                $service = new \Google_Service_Drive($client);
                $adapter = new GoogleDriveAdapter($service);
                $fileSystem = new Filesystem($adapter);
            }
            catch (\Exception $e) {
                $errorMessage = 'An invalid exception was thrown. Message: ' . $e->getMessage();
                Storage::disk('local')->delete('config.json');
                $request->session()->forget('access_token');
                $request->session()->flash('message', $errorMessage);
                return redirect()->route('home.index');
            }
            catch (\Google_Exception $e) {
                $errorMessage = 'Google_Exception: ' . $e->getMessage();
                $request->session()->flash('message', $errorMessage);
                return redirect()->route('home.index');
            }
        } else {
            return redirect()->route('home.index');
        }

        $server = new Server('imap.gmail.com');

        try {
            $connection = $server->authenticate($request->get('email'), $request->get('password'));
            $mailbox = $connection->getMailbox('INBOX');
            $messages = $mailbox->getMessages();

            foreach ($messages as $message) {
                $attachments = $message->getAttachments();
                foreach ($attachments as $attachment) {
                    if ($attachment instanceof Attachment) {
                        if (FileHelper::isPdf($attachment->getSubtype()) && FileHelper::isValidSize($attachment->getBytes())) {
                            FileHelper::store($attachment->getFilename(), $attachment->getDecodedContent(), $fileSystem);
                        }
                    }
                }
            }
            $request->session()->flash('message', 'Files have been uploaded to Google Drive successfully.');
        }
        catch (AuthenticationFailedException $e) {
            $errorMessage = 'AuthenticationFailedException: ' . $e->getMessage();
            $request->session()->flash('message', $errorMessage);
        }
        catch (MailboxDoesNotExistException $e) {
            $errorMessage = 'MailboxDoesNotExistException: ' . $e->getMessage();
            $request->session()->flash('message', $errorMessage);
        }
        catch (Exception $e) {
            $errorMessage = 'An invalid exception was thrown. Message: ' . $e->getMessage();
            $request->session()->flash('message', $errorMessage);
        }

        return redirect()->route('home.index');
    }
}