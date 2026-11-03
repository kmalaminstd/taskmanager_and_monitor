<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\TaskSubmission;
use App\Models\TaskExtensionRequest;
use App\Models\TaskTransferRequest;
use App\Services\ApprovalService;
use App\Services\TransferService;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function __construct(
        protected ApprovalService $approvalService,
        protected TransferService $transferService
    ) {}

    public function index()
    {
        $pendingSubmissions = TaskSubmission::with(['assignment.task', 'assignment.assignee', 'submitter'])
            ->where('status', 'pending')->latest()->get();
        $pendingExtensions = TaskExtensionRequest::with(['assignment.task', 'requester'])
            ->where('status', 'pending')->latest()->get();
        $pendingTransfers = TaskTransferRequest::with(['assignment.task', 'requester', 'newAssignee'])
            ->where('status', 'pending')->latest()->get();

        return view('supervisor.approvals.index', compact(
            'pendingSubmissions', 'pendingExtensions', 'pendingTransfers'
        ));
    }

    public function approveSubmission(Request $request, TaskSubmission $submission)
    {
        $data = $request->validate(['note' => 'nullable|string|max:1000']);
        $this->approvalService->approveSubmission($submission, auth()->user(), $data['note'] ?? null);
        return back()->with('success', 'Submission approved.');
    }

    public function returnSubmission(Request $request, TaskSubmission $submission)
    {
        $data = $request->validate(['return_note' => 'required|string|max:1000']);
        $this->approvalService->returnSubmission($submission, auth()->user(), $data['return_note']);
        return back()->with('success', 'Submission returned.');
    }

    public function approveExtension(Request $request, TaskExtensionRequest $extension)
    {
        $data = $request->validate(['admin_note' => 'nullable|string|max:1000']);
        $this->approvalService->approveExtension($extension, auth()->user(), $data['admin_note'] ?? null);
        return back()->with('success', 'Extension approved.');
    }

    public function rejectExtension(Request $request, TaskExtensionRequest $extension)
    {
        $data = $request->validate(['admin_note' => 'nullable|string|max:1000']);
        $this->approvalService->rejectExtension($extension, auth()->user(), $data['admin_note'] ?? null);
        return back()->with('success', 'Extension rejected.');
    }

    public function approveTransfer(Request $request, TaskTransferRequest $transfer)
    {
        $data = $request->validate(['admin_note' => 'nullable|string|max:1000']);
        $this->transferService->approveTransferRequest($transfer, auth()->user(), $data['admin_note'] ?? null);
        return back()->with('success', 'Transfer approved.');
    }

    public function rejectTransfer(Request $request, TaskTransferRequest $transfer)
    {
        $data = $request->validate(['admin_note' => 'nullable|string|max:1000']);
        $this->transferService->rejectTransferRequest($transfer, auth()->user(), $data['admin_note'] ?? null);
        return back()->with('success', 'Transfer rejected.');
    }
}
