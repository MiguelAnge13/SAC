<?php

namespace App\Services;

use App\Models\EventLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventLogger
{
    /**
     * Registra un evento.
     *
     * @param  string $action
     * @param  string|null $entity
     * @param  int|null $entityId
     * @param  string|null $description
     * @param  array|null $meta
     * @return EventLog
     */
    public static function log(string $action, ?string $entity = null, ?int $entityId = null, ?string $description = null, ?array $meta = null, ?Request $request = null)
    {
        $request = $request ?: request();

        $data = [
            'user_id'     => Auth::check() ? Auth::id() : null,
            'action'      => $action,
            'entity'      => $entity,
            'entity_id'   => $entityId,
            'description' => $description,
            'ip'          => $request ? $request->ip() : null,
            'user_agent'  => $request ? substr((string) $request->header('User-Agent'), 0, 500) : null,
            'meta'        => $meta,
        ];

        return EventLog::create($data);
    }
}
