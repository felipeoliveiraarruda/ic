<?php

namespace App\Mail;

use App\Models\Projeto;
use App\Models\Inscricao;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class InscricaoProjetoMail extends Mailable
{
use Queueable, SerializesModels;

    public function __construct(public Projeto $projeto, public Inscricao $inscricao) 
    {

    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '['.env('APP_NAME').'] Nova Inscrição Recebida: ' . $this->projeto->tituloProjeto,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.projetos.inscricao',
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        // Anexa o Histórico Escolar ao e-mail se o arquivo existir no Storage
        if ($this->inscricao->historico_escolar && Storage::disk('public')->exists($this->inscricao->historico_escolar)) 
        {
            $attachments[] = Attachment::fromPath(Storage::disk('public')->path($this->inscricao->historico_escolar))
                ->as('Historico_Escolar_' . str_replace(' ', '_', $this->inscricao->nome) . '.pdf')
                ->withMime('application/pdf');
        }

        return $attachments;
    }
}
