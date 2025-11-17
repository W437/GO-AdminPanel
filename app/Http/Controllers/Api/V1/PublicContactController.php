<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PublicContactController extends Controller
{
    /**
     * Submit Contact Form
     *
     * Submit a contact form message from the public website.
     * This endpoint is for the React website contact page.
     *
     * @group Public Contact API
     * @unauthenticated
     *
     * @bodyParam name string required Full name. Example: John Doe
     * @bodyParam email string required Email address. Example: john@example.com
     * @bodyParam mobile_number string optional Phone number. Example: +972501234567
     * @bodyParam subject string optional Subject/reason for contact. Example: Question about delivery
     * @bodyParam message string required Message content. Example: I would like to know about your delivery times.
     *
     * @response 201 {
     *   "message": "Thank you for contacting us! We'll get back to you soon.",
     *   "contact_id": 15
     * }
     *
     * @response 422 {
     *   "errors": [
     *     {"code": "name", "message": "The name field is required"},
     *     {"code": "email", "message": "The email field is required"}
     *   ]
     * }
     */
    public function submit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile_number' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ], [
            'name.required' => translate('messages.name_is_required'),
            'email.required' => translate('messages.email_is_required'),
            'email.email' => translate('messages.email_must_be_valid'),
            'message.required' => translate('messages.message_is_required'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => array_map(function ($field, $messages) {
                    return [
                        'code' => $field,
                        'message' => $messages[0]
                    ];
                }, array_keys($validator->errors()->messages()), $validator->errors()->messages())
            ], 422);
        }

        try {
            $contactMessage = new ContactMessage();
            $contactMessage->name = $request->name;
            $contactMessage->email = $request->email;
            $contactMessage->mobile_number = $request->mobile_number;
            $contactMessage->subject = $request->subject ?? 'Contact Form Submission';
            $contactMessage->message = $request->message;
            $contactMessage->seen = 0;
            $contactMessage->status = 1;
            $contactMessage->save();

            return response()->json([
                'message' => translate('messages.contact_message_sent_successfully')
                    ?? 'Thank you for contacting us! We will get back to you soon.',
                'contact_id' => $contactMessage->id
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    ['code' => 'server', 'message' => 'Failed to submit contact form. Please try again.']
                ]
            ], 500);
        }
    }
}
