<div id="specificationHolder-{$id}" class="options specificationInput">
    {option:spec}
        <table class="specifications">
            {option:languages}
                <tr>
                    <td>&nbsp;</td>
                    {iteration:languages}
                        <th style="padding-left:20px;">

                            {$languages.language|ucfirst}
                        </th>
                    {/iteration:languages}
                </tr>
            {/option:languages}
            <tr>
                <td>

                    <label for="specification{$id}">
                        {$label}{option:required}<abbr title="{$lblRequiredField}">*</abbr>{/option:required}
                    </label>
                </td>

                {option:fields}
                {iteration:fields}
                    <td>
                        {$fields.field}
                    </td>
                {/iteration:fields}
                {/option:fields}
                {*{$field}*}
            </tr>
        </table>
    {/option:spec}
</div>