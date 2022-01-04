@component('mail.html.message')
    <p>
        <span >LIGNE WEB SERVICES</span>
        <br >
        <span >Département Sécurité Chicorycom</span>
        <br >
        <span >Zac MBAO sité SAGEF 2</span>
        <br >
        <span >00221 DAKAR</span>
        <br >
        <a href="https://chicorycom.net/" rel="noreferrer" target="_blank"  style="color: rgb(17, 85, 204); font-family: Arial, Helvetica, sans-serif; font-size: small; background-color: rgb(255, 255, 255);">https://www.chicorycom.net</a>
        <br ><br ><br >
        <span >&nbsp; DAKAR le: {{ $date }}</span>
        <br ><span >&nbsp; Objet : Notification de connexion à votre espace client INFOSCHOOL.</span>
        <br ><br ><br >
        <span >Chèr(e) {{ $user->prenom . ' ' . $user->nom }},</span>
        <br >
        <br >
        <br >
        <span >Nous vous envoyons cet email à la suite d'une connexion réussie à votre interface client.</span>
        <br >
        <br >
        <span >&nbsp; &nbsp; Identifiant client&nbsp; : {{ $user->email }}</span>
        <br >
        <span >&nbsp; &nbsp; Ip de connexion&nbsp; &nbsp; &nbsp;: {{ $ip }}</span>
        <br ><span >&nbsp; &nbsp; Heure de connexion&nbsp; : {{ $datetime }}</span>
        <br >
        <br >
        <br >
        <br >
        <span >Cet email est destiné à vous sensibiliser à la sécurité des services que vous avez chez CHICORYCOM et à mieux les protéger.</span>
        <br >
        <br >
        <span >Pour modifier les réglages de ces alertes, rendez-vous dans votre espace client, dans la rubrique Coordonnées.</span>
        <br ><br ><span >Pour tout savoir sur l'utilisation de l'identifiant client chez Infoschool:</span>
        <br >
        <a href="https://aide.chicorycom.net" rel="noreferrer" target="_blank"  style="color: rgb(17, 85, 204); font-family: Arial, Helvetica, sans-serif; font-size: small; background-color: rgb(255, 255, 255);">https://aide.chicorycom<wbr>.net</a>
        <br ><br >
        <span >Nous vous remercions pour la confiance que vous nous accordez.</span>
        <br ><br ><br ><span >Cordialement,</span>
        <br >
        <span >L'équipe Ligne Web Services, disponible 7j/7 via votre espace client</span>
        <br >
        <span >- Pour une assistance technique</span>
        <br >
        <span >- Pour une assistance commerciale</span>
        <br ><br ><span >Ceci est un mail automatique, vous ne pouvez pas y répondre.</span>
        <br ><span >Contactez-nous directement via votre espace client via la rubrique ASSISTANCE.</span>
        <!--br ><span >(ref mail : 77)</span><br> -->
    </p>
@endcomponent
