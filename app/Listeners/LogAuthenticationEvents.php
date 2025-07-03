<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use App\Models\AuditLog; // Make sure to import your AuditLog model
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User; // Import the User model

class LogAuthenticationEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle user login events.
     */
    public function handleUserLogin(Login $event)
    {
        AuditLog::create([
            'user_id' => $event->user->id,
            'action' => 'login', // Using the existing 'action' column
            'model_type' => get_class($event->user), // Or just User::class
            'model_id' => $event->user->id,
            'changes' => json_encode(['description' => 'User logged in successfully']), // Store description in changes
            'ip_address' => $this->request->ip(),
        ]);
    }

    /**
     * Handle user logout events.
     */
    public function handleUserLogout(Logout $event)
    {
        // For logout, $event->user might be null if session is already destroyed
        $userId = $event->user ? $event->user->id : null;
        $modelType = $event->user ? get_class($event->user) : null;
        $modelId = $event->user ? $event->user->id : null;

        AuditLog::create([
            'user_id' => $userId,
            'action' => 'logout', // Using the existing 'action' column
            'model_type' => $modelType,
            'model_id' => $modelId,
            'changes' => json_encode(['description' => 'User logged out']), // Store description in changes
            'ip_address' => $this->request->ip(),
        ]);
    }

    /**
     * Handle user failed login events.
     */
    public function handleUserFailedLogin(Failed $event)
    {
        // For failed login, $event->user will be null
        AuditLog::create([
            'user_id' => null, // No authenticated user for failed attempts
            'action' => 'failed_login', // Using the existing 'action' column
            'model_type' => null, // No specific model involved
            'model_id' => null,   // No specific model involved
            'changes' => json_encode([
                'description' => 'Failed login attempt',
                'credentials' => $event->credentials // Log the attempted credentials (e.g., email/username)
            ]),
            'ip_address' => $this->request->ip(),
        ]);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(
            Login::class,
            [LogAuthenticationEvents::class, 'handleUserLogin']
        );

        $events->listen(
            Logout::class,
            [LogAuthenticationEvents::class, 'handleUserLogout']
        );

        $events->listen(
            Failed::class,
            [LogAuthenticationEvents::class, 'handleUserFailedLogin']
        );
    }
}
