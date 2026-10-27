<?php
final class Security { public static function field(): string { return ''; } }
function esc($value): string { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
function e($value): string { return esc($value); }
function old($key, $default = ''): string { return esc($default); }
function isAuth(): bool { return $GLOBALS['demoAuthenticated']; }
function isAdmin(): bool { return $GLOBALS['demoAdmin']; }
function user(): ?array { return isAuth() ? ['name' => 'Посетитель'] : null; }
function statusBadge($value): string { return ['new'=>'warning','confirmed'=>'success','rejected'=>'danger'][$value] ?? 'secondary'; }
