<?php

namespace App;

class Helpers
{
    /**
     * Format time from centiseconds to readable format
     */
    public static function formatTime(?int $centiseconds): string
    {
        if ($centiseconds === null) {
            return 'DNF';
        }
        
        if ($centiseconds < 0) {
            return 'DNF';
        }

        $totalSeconds = $centiseconds / 100;
        $minutes = floor($totalSeconds / 60);
        $seconds = $totalSeconds - ($minutes * 60);

        if ($minutes > 0) {
            return sprintf('%d:%05.2f', $minutes, $seconds);
        }
        
        return sprintf('%.2f', $seconds);
    }

    /**
     * Check if user is authenticated
     */
    public static function isAuthenticated(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }

    /**
     * Get current user ID
     */
    public static function currentUserId(): ?int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Format date
     */
    public static function formatDate(?string $date, string $format = 'd.m.Y'): string
    {
        if (!$date) {
            return '';
        }
        
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            return $date;
        }
        
        return date($format, $timestamp);
    }

    /**
     * Check if current user is admin
     */
    public static function isAdmin(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
    }

    /**
     * Sanitize HTML output
     */
    public static function e(?string $string): string
    {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}
