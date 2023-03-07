@extends("layouts.email")
@section("emailbody")	
<!-- big image section -->
<table align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-bottom: 20px solid transparent; border-radius: 10px; color: #000000; width: 600px;" width="600">
<tbody>
<tr>
<td class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 5px; padding-bottom: 0px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="100%">
<table border="0" cellpadding="0" cellspacing="0" class="heading_block block-1" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
<tr>
<td class="pad" style="width:100%;text-align:center;">
<h2 style="margin: 0; color: #8e0045; font-size: 30px; font-family: 'Oxygen', 'Trebuchet MS', 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Tahoma, sans-serif; line-height: 180%; text-align: center; direction: ltr; font-weight: 400; letter-spacing: 3px; margin-top: 0; margin-bottom: 0;"><span class="tinyMce-placeholder">{!!$details['title']!!}</span></h2>
</td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" class="paragraph_block block-2" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
<tr>
<td class="pad" style="padding-bottom:15px;">
<div style="color:#242424;font-size:16px;font-family:'Oxygen', 'Trebuchet MS', 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Tahoma, sans-serif;font-weight:400;line-height:180%;text-align:left;direction:ltr;letter-spacing:0px;mso-line-height-alt:28.8px;">{!!$details['body']!!}
</div>
</td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" class="button_block block-3" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
<tr>
<td class="pad" style="text-align:center;">
<div align="center" class="alignment">
<!--[if mso]><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" style="height:40px;width:396px;v-text-anchor:middle;" arcsize="13%" stroke="false" fillcolor="#8e0045"><w:anchorlock/><v:textbox inset="0px,0px,0px,0px"><center style="color:#ffffff; font-family:'Trebuchet MS', Tahoma, sans-serif; font-size:14px"><![endif]-->
@if($details['button'] != '')
<div style="text-decoration:none;display:inline-block;color:#ffffff;background-color:#8e0045;border-radius:5px;width:auto;border-top:0px solid transparent;font-weight:400;border-right:0px solid transparent;border-bottom:0px solid transparent;border-left:0px solid transparent;padding-top:5px;padding-bottom:5px;font-family:'Oxygen', 'Trebuchet MS', 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Tahoma, sans-serif;font-size:14px;text-align:center;mso-border-alt:none;word-break:keep-all;"><a href="{!!$details['link']!!}" style="color:#FFF"><span style="padding-left:20px;padding-right:20px;font-size:14px;display:inline-block;letter-spacing:normal;"><span dir="ltr" style="word-break: break-word; line-height: 28px;text-align:center">{!!$details['button']!!}</span></span></a></div>
<!--[if mso]></center></v:textbox></v:roundrect><![endif]-->
@endif
</div>
</td>
</tr>
</table>

<table border="0" cellpadding="0" cellspacing="0" class="paragraph_block block-2" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
<tr>
<td class="pad" style="padding-bottom:15px;">
<div style="color:#242424;font-size:16px;font-family:'Oxygen', 'Trebuchet MS', 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Tahoma, sans-serif;font-weight:400;line-height:180%;text-align:center;direction:ltr;letter-spacing:0px;mso-line-height-alt:28.8px;">{!!$details['body1']!!}
</div>
</td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" class="button_block block-3" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
<tr>
<td class="pad" style="text-align:center;">
<div align="center" class="alignment">
<!--[if mso]><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" style="height:40px;width:396px;v-text-anchor:middle;" arcsize="13%" stroke="false" fillcolor="#8e0045"><w:anchorlock/><v:textbox inset="0px,0px,0px,0px"><center style="color:#ffffff; font-family:'Trebuchet MS', Tahoma, sans-serif; font-size:14px"><![endif]-->
@if($details['button1'] != '')
<br/><br/>
<div style="text-decoration:none;display:inline-block;color:#ffffff;background-color:#8e0045;border-radius:5px;width:auto;border-top:0px solid transparent;font-weight:400;border-right:0px solid transparent;border-bottom:0px solid transparent;border-left:0px solid transparent;padding-top:5px;padding-bottom:5px;font-family:'Oxygen', 'Trebuchet MS', 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Tahoma, sans-serif;font-size:14px;text-align:center;mso-border-alt:none;word-break:keep-all;"><a href="{!!$details['link']!!}" style="color:#FFF"><span style="padding-left:20px;padding-right:20px;font-size:14px;display:inline-block;letter-spacing:normal;"><span dir="ltr" style="word-break: break-word; line-height: 28px;text-align:center">{!!$details['button1']!!}</span></span></a></div>
<!--[if mso]></center></v:textbox></v:roundrect><![endif]-->
@endif
</div>
</td>
</tr>
</table>

<table border="0" cellpadding="0" cellspacing="0" class="paragraph_block block-5" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
<tr>
<td class="pad" style="padding-top:5px;">
<div style="color:#101112;font-size:12px;font-family:'Oxygen', 'Trebuchet MS', 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Tahoma, sans-serif;font-weight:400;line-height:150%;text-align:center;direction:ltr;letter-spacing:0px;mso-line-height-alt:18px;">
<p style="margin: 0;text-align:center"><strong>Email</strong>: return@deluxerie.net</p>
</div>
</td>
</tr>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>    
@endsection	