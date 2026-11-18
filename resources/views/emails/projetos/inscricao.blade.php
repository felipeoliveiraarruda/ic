
@component('mail::message')
# Nova Inscrição de Iniciação Científica

Prezado(a),

Uma nova inscrição foi submetida para o projeto **{{ $projeto->tituloProjeto }}**.

### Dados do Candidato:
- **Nome:** {{ $inscricao->nome }}
- **E-mail:** {{ $inscricao->email }}
- **Curso:** {{ $inscricao->cursoInscricao ?? 'Não informado' }}
- **Instituição** {{ $inscricao->instituicaoInscricao ?? 'Não informado' }}

---
*O histórico escolar do aluno foi anexado a este e-mail.*

Atenciosamente,<br>
Sistema de Gestão de Projetos IC
@endcomponent