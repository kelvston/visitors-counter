<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function index()
    {
        return view('chatbot.index');
    }

    public function respond(Request $request)
{
    $userMessage = $request->input('message');
    $userMessage = strtolower(trim($userMessage)); // Normalize the input

    // Define some simple responses
    $responses = [
        'hello' => 'Hello! How can I assist you today?',
        'help' => 'I can help you with various tasks. What do you need assistance with?',
        'bye' => 'Goodbye! Have a wonderful day!',
        'how are you' => 'I’m just a bot, but I’m doing great! How can I help you?',
        'what is your name' => 'I am a chatbot created to assist you. How can I help you today?',
        'thank you' => 'You’re welcome! If you have more questions, feel free to ask.'
    ];

    // Define responses for more complex queries
    $complexResponses = [
        'weather' => 'I can’t check the weather, but you can use a weather app or website for that.',
        'joke' => 'Why don’t scientists trust atoms? Because they make up everything!',
        'news' => 'I’m not able to provide news updates, but you can check news websites for the latest information.'
    ];

    // Match user message with responses
    foreach ($responses as $keyword => $response) {
        if (strpos($userMessage, $keyword) !== false) {
            return response()->json(['message' => $response]);
        }
    }

    foreach ($complexResponses as $keyword => $response) {
        if (strpos($userMessage, $keyword) !== false) {
            return response()->json(['message' => $response]);
        }
    }

    // Default response if no match is found
    return response()->json(['message' => "I'm sorry, I didn't understand that."]);
}

}
