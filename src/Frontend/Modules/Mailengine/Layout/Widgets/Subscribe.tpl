<h4>{$lblNewsletter}</h4>
<p>{$msgNewsletter}</p>
<div class="row">
    <div class="col-sm-8">


        <form action="{$var|geturlforblock:'Mailengine':'MailengineSubscribe'}" method="post">
            <input type="hidden" name="form" value="subscribe"/>

            <input type="text" value="" id="email" name="email" class="form-control" placeholder="{$lblEmail|ucfirst}"/>

            <div class="clearfix">&nbsp;</div>

            <input id="send" class="btn btn-success" type="submit" name="send" value="{$lblSubscribe|ucfirst}"/>

            <div class="clearfix">&nbsp;</div>

        </form>

    </div>
    <!-- /.col-sm-8 -->
</div>
<!-- /.row -->