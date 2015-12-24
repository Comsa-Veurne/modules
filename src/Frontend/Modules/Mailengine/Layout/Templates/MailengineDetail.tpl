
{option:item}
	<h3>{$lblMailing|ucfirst} {$item.subject}</h3>

    <time>{$item.start_time|date:{$dateFormatLong}:{$LANGUAGE}}</item>

    <iframe src="{$iframe}" style="width:800px;height:600px;border:0;" id="iframe"></iframe>

{/option:item}
<a href="{$var|geturlforblock:'mailengine':'index'}">{$lblBackToMailings|ucfirst}</a>
