<?php

namespace App\Http\Controllers;

use App\Http\Requests\MailboxRequest;
use App\Models\Mailbox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MailboxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $mailbox = new Mailbox();
        return view('mailboxes.create', compact('mailbox'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MailboxRequest $request)
    {
        Auth::user()->mailboxes()->create($request->only('name', 'host', 'port', 'encryption', 'email', 'password', 'is_active'));
        flash('Your mailbox was successfully created')->success();

        return redirect(route('home'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Mailbox  $mailbox
     * @return \Illuminate\Http\Response
     */
    public function show(Mailbox $mailbox)
    {
        return redirect(route('mailbox.edit', $mailbox));
    }

    /**
     * @param Mailbox $mailbox
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Mailbox $mailbox)
    {
        $this->authorize('edit', $mailbox);

        return view('mailboxes.edit', compact('mailbox'));
    }

    /**
     * @param MailboxRequest $request
     * @param Mailbox $mailbox
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(MailboxRequest $request, Mailbox $mailbox)
    {
        $this->authorize('edit', $mailbox);

        $mailbox->update($request->only('name', 'host', 'port', 'encryption', 'email', 'password', 'is_active'));
        flash('Your mailbox was successfully updated')->success();

        return redirect(route('home'));
    }

    /**
     * @param Mailbox $mailbox
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Mailbox $mailbox)
    {
        $this->authorize('destroy', $mailbox);

        $mailbox->delete();

        flash('Your mailbox was successfully deleted')->success();
        return redirect(route('home'));
    }
}
