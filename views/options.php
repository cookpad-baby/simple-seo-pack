<div class="wrap">
 
<h2>Simple SEOの設定</h2>
 
<form method="post" action="options.php">
 
<?php
     settings_fields( 'hello-world-group' );
     do_settings_sections( 'default' );
?>
 
<table class="form-table">
     <tbody>
     <tr>
          <th scope="row"><label for="active_twitter">ホームのキーワード</label></th>
          <td>
              <textarea name="home_keyword" id="home_keyword" cols="50" rows="5"></textarea>
          </td>
     </tr>
     </tbody>
</table>

<?php submit_button(); // 送信ボタン ?>
 
</form>
 
</div><!-- .wrap -->