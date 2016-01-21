{include:/Core/Layout/Templates/Mails/Header.tpl}
    <h1>Nieuwe bestelling</h1>
<p>Er is een nieuwe bestelling geplaatst op {$SITE_DOMAIN}.</p>
<p>
    <b>Naam:</b> {$fname} {$lname} <br/>
    <b>Adres:</b> {$address} {$hnumber}, {$postal} {$hometown} <br/>
    <b>Totaal:</b> &euro; {$total}
</p>
{include:/Core/Layout/Templates/Mails/Footer.tpl}
