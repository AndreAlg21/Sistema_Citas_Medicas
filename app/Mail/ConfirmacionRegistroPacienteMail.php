<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfirmacionRegistroPacienteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $usuario)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Cuenta creada en Clínica Vida Salud');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registro-paciente',
            with: ['usuario' => $this->usuario]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
