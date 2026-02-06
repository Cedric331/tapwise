<p>Vous avez reçu un nouveau message depuis le formulaire de contact.</p>

<p><strong>Nom :</strong> {{ $payload['name'] }}</p>
<p><strong>Email :</strong> {{ $payload['email'] }}</p>
<p><strong>Sujet :</strong> {{ $payload['subject'] }}</p>

<p><strong>Message :</strong></p>
<p>{!! nl2br(e($payload['message'])) !!}</p>

