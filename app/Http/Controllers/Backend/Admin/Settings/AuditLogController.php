<?php

namespace App\Http\Controllers\Backend\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\AuditRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OwenIt\Auditing\Models\Audit;

class AuditLogController extends Controller
{
    public function __construct(
        private readonly AuditRepositoryInterface $auditRepository,
    ) {}

    public function index(Request $request): View
    {
        $event = $request->string('action')->toString() ?: null;
        $auditableType = $request->string('model')->toString() ?: null;
        $fromDate = $request->string('from')->toString() ?: null;
        $toDate = $request->string('to')->toString() ?: null;

        $audits = $this->auditRepository->paginateFiltered(
            $event,
            $auditableType,
            $fromDate,
            $toDate,
        );

        $modelOptions = $this->auditRepository->distinctAuditableTypes();

        $actionOptions = [
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'restored' => 'Restored',
        ];

        return view('backend::admin.settings.audit-logs.index', compact(
            'audits',
            'modelOptions',
            'actionOptions',
        ));
    }

    public function show(Audit $audit): View
    {
        $audit->loadMissing('user');

        return view('backend::admin.settings.audit-logs.show', [
            'audit' => $audit,
        ]);
    }
}
