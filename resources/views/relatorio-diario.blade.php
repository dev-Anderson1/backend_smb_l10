<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1, h2, h3 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        td, th { border: 1px solid #000; padding: 6px; text-align: left; }
        .header, .footer { text-align: center; margin-top: 20px; }
        .footer { font-size: 11px; margin-top: 40px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>{{ $relatorio->instituicao }}</h2>
        <h3>Relatório Diário do Furriel</h3>
        <p><b>Data:</b> {{ \Carbon\Carbon::parse($relatorio->data)->format('d/m/Y') }}</p>
    </div>

    <table>
        <tr>
            <td><b>Oficial de Dia:</b></td>
            <td>{{ $relatorio->oficialDia->apelido ?? '—' }}</td>
        </tr>
        <tr>
            <td><b>Respondente:</b></td>
            <td>{{ $relatorio->respondente->apelido ?? '—' }}</td>
        </tr>
        <tr>
            <td><b>Adjunto:</b></td>
            <td>{{ $relatorio->adjunto->apelido ?? '—' }}</td>
        </tr>
        <tr>
            <td><b>Dia SMB:</b></td>
            <td>{{ $relatorio->diaSmb->apelido ?? '—' }}</td>
        </tr>
    </table>

    <h3>Acontecimentos do Dia</h3>
    <p>{{ $relatorio->acontecimentos ?? 'Nenhum registro.' }}</p>

   <div class="footer">
  <p>
    <b>Dia SMB entra:</b> {{ $relatorio->diaSmb->apelido ?? $relatorio->diaSmbEntrada->name ?? '—' }}<br>
    <b>Dia SMB sai:</b> {{ $relatorio->diaSmbSaida->apelido ?? $relatorio->diaSmbSaida->name ?? '—' }}
  </p>
</div>


</body>
</html>
