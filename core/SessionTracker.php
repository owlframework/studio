<?php
namespace Core;

/**
 * Session & Traffic Tracker
 * 
 * Handles active user session tracking, telemetry logging, and automated session garbage collection.
 */
// Prevent direct script access by verifying application root definition.
defined('ROOTPATH') OR die("Direct access denied.");

class SessionTracker extends Model {
    protected $table = 'sessions';
    protected $allowedColumns = ['id', 'user_id', 'last_activity'];

    /**
     * Track and update the current user's session state and trigger probabilistic garbage collection.
     *
     * @return void
     */
    public function track() {
        $session_id = session_id();
        $user_id = Session::get(AUTH_SESSION);
        $time = time();

        // Perform upsert operation to record or refresh session activity using model methods.
        $this->upsert(
            [
                'id' => $session_id, 
                'user_id' => $user_id, 
                'last_activity' => $time
            ],
            [
                'user_id' => $user_id, 
                'last_activity' => $time
            ]
        );

        // Execute probabilistic garbage collection cleanup (2% execution probability).
        if (rand(1, 100) <= 2) {
            $this->gc(24); // Purge stale sessions inactive for more than 24 hours.
        }
    }

    /**
     * Clean up expired session records exceeding the specified age threshold.
     *
     * @param int $hours Maximum age in hours of session history to retain
     * @return mixed Deletion query execution result
     */
    public function gc($hours = 24) {
        $threshold = time() - ($hours * 3600);
        
        // Remove outdated session entries using custom deletion criteria.
        return $this->deleteWhere("last_activity < :old", ['old' => $threshold]);
    }

    /**
     * Retrieve the count of active users currently online within a specified time window.
     *
     * @param int $minutes Time window in minutes for defining user activity
     * @return int Number of active users matching the threshold
     */
    public function getOnlineCount($minutes = 5): int {
        $threshold = time() - ($minutes * 60);
        
        // Query active user count using custom constraint filtering.
        return $this->countWhere("last_activity >= :time", ['time' => $threshold]);
    }
}