<tr>
    <td>
        <table
                style="padding: 0;
                        text-align: center;
                        width: 570px;
                        -premailer-cellpadding: 0;
                        -premailer-cellspacing: 0;
                        -premailer-width: 570px;
                        color: #aeaeae;
                        font-size: 12px;
                        text-align: center;" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
                <td class="content-cell" align="center">
                    {{ \Illuminate\Mail\Markdown::parse($slot) }}
                </td>
            </tr>
        </table>
    </td>
</tr>
