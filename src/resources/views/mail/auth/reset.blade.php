@component('mail.html.message')
    Salut {{ $name }}, <br/>
    vous avez des difficultés à se connecter?<br/>
    La réinitialisation de votre mot de passe est facile.
    Appuyez simplement sur le bouton ci-dessous et suivez les instructions. Nous vous aiderons à être opérationnel en un rien de temps.
    @component('mail.html.button', compact('url'))
        Cliquez ici pour voir le lien !
    @endcomponent
    Si vous n'avez pas fait cette demande, veuillez ignorer cet e-mail.
@endcomponent


