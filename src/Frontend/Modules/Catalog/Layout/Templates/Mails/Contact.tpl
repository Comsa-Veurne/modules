{include:/Core/Layout/Templates/Mails/Header.tpl}

<h2>{$SITE_TITLE}: {$lblContact|ucfirst}: {$product}</h2>
<hr/>
<strong>{$lblName|ucfirst}:</strong> {$name}<br/>
<strong>{$lblPhone|ucfirst}:</strong> {$phone}<br/>
<strong>{$lblEmail|ucfirst}:</strong> {$email}<br/>
<strong>{$lblMessage|ucfirst}:</strong> {$message}<br/>
<hr/>

{include:/Core/Layout/Templates/Mails/Footer.tpl}
