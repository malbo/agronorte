/**
 * Connector path
 */
if (location.host === 'localhost')
{
    var connector = '../frontend/inc/Connector.php'; 
}
else
{
    var connector = location.protocol + '//' + location.host + '/inc/Connector.php';
}

    /**
     * Diference between arrays
     * 
     * @param arrays
     * @return array
     */
    function arr_diff (a1, a2) 
    {
        var a = [], diff = [];
        for (var i = 0; i < a1.length; i++) {
            a[a1[i]] = true;
        }

        for (var i = 0; i < a2.length; i++) {
            if (a[a2[i]]) {
                delete a[a2[i]];
            } else {
                a[a2[i]] = true;
            }
        }

        for (var k in a) {
            diff.push(k);
        }

        return diff;
    }
    
    /**
     * JS implementation of PHP base64_decode
     *  
     * @return array
     */
    function base64_decode (data) 
    {
      var b64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/='
      var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
        ac = 0,
        dec = '',
        tmp_arr = []

      if (!data) {
        return data
      }

      data += ''

      do {
        // unpack four hexets into three octets using index points in b64
        h1 = b64.indexOf(data.charAt(i++))
        h2 = b64.indexOf(data.charAt(i++))
        h3 = b64.indexOf(data.charAt(i++))
        h4 = b64.indexOf(data.charAt(i++))

        bits = h1 << 18 | h2 << 12 | h3 << 6 | h4

        o1 = bits >> 16 & 0xff
        o2 = bits >> 8 & 0xff
        o3 = bits & 0xff

        if (h3 == 64) {
          tmp_arr[ac++] = String.fromCharCode(o1)
        } else if (h4 == 64) {
          tmp_arr[ac++] = String.fromCharCode(o1, o2)
        } else {
          tmp_arr[ac++] = String.fromCharCode(o1, o2, o3)
        }
      } while (i < data.length)

      dec = tmp_arr.join('')

      return decodeURIComponent(escape(dec.replace(/\0+$/, '')))
    }
    
    /**
     * Copy to clipboard
     *  
     * @return array
     */
    function copyToClipboard(text)
    {
        window.prompt ("Copy to clipboard: Ctrl C, Enter", text);
    }
    
    /**
     * Explode PHP implementation
     * 
     * @return boolean
     */
    function explode (delimiter, string, limit) 
    {
        if (arguments.length < 2 || typeof delimiter === 'undefined' || typeof string === 'undefined') return null
        if (delimiter === '' || delimiter === false || delimiter === null) return false
        if (typeof delimiter === 'function' || typeof delimiter === 'object' || typeof string === 'function' || typeof string ===
          'object') {
          return {
            0: ''
          };
        }
        if (delimiter === true) delimiter = '1';

        // Here we go...
        delimiter += '';
        string += '';

        var s = string.split(delimiter)

        if (typeof limit === 'undefined') return s;

        // Support for limit
        if (limit === 0) limit = 1;

        // Positive limit
        if (limit > 0) {
          if (limit >= s.length) return s;
          return s.slice(0, limit - 1)
            .concat([s.slice(limit - 1)
              .join(delimiter)
            ]);
        }

        // Negative limit
        if (-limit >= s.length) return [];

        s.splice(s.length + limit);
        return s;
    }
    
    /**
     * Process login form
     * 
     * @return array
     */
    function login()
    {
        // get data
        var email       = $('#email').val();
        var password    = $('#password').val();
        var security    = $('.g-recaptcha-response').val();

        console.log("EMAIL: " + email);
        console.log("PASSWORD: " + password);
        console.log("SECURITY: " + security);

        // create params
        var params = {
            class:      'Secure',
            method:     'login',
            email:      email,
            password:   password,
            security:   security
        };

        $.post(connector, {'params':params}, function(e){
            var data = jQuery.parseJSON(e);
            if(true === data.success)
            {
                if (session === 'expired')
                {
                    window.location = file;
                }
                else
                {
                    window.location = data.location;
                }  
            }
            else
            {
                notification('error', data.message);   
            }
        }); 
    }
    
    /**
     * Format numbers to M, B, etc
     *  
     * @return array
     */
    function nFormatter(num, digits) {
      var si = [
        { value: 1, symbol: "" },
        { value: 1E3, symbol: "k" },
        { value: 1E6, symbol: "m" },
        { value: 1E9, symbol: "b" },
        { value: 1E12, symbol: "T" },
        { value: 1E15, symbol: "P" },
        { value: 1E18, symbol: "E" }
      ];
      var rx = /\.0+$|(\.[0-9]*[1-9])0+$/;
      var i;
      for (i = si.length - 1; i > 0; i--) {
        if (num >= si[i].value) {
          break;
        }
      }
      return (num / si[i].value).toFixed(digits).replace(rx, "$1") + si[i].symbol;
    }
    
    /**
     * Notifications
     * 
     * @param {string} type
     * @param {string} message
     */
    function notification(type, message)
    {
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-center",
            "preventDuplicates": true,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "2500",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
        
        switch(type)
        {
            case 'info':
                toastr.info(message);
                break;
                
            case 'success':
                toastr.success(message);
                break;
                
            case 'warning':
                toastr.warning(message);
                break;
                
            case 'error':
                toastr.error(message);
                break;
                
            default:
                break;      
        }
        
    }
    
    /**
     * Store admin
     *  
     * @return string
     */
    function storeAdmin()
    {
        var id = empty($('#usr-id_user').val()) ? null : $('#usr-id_user').val();
        
        // validations
        if (true === empty($('#usr-company').val()) || true === empty($('#usr-category').val()) || true === empty($('#usr-country').val()))
        {
            notification('error', 'Complete all fields.');           
            return false;
        }
        
        // create params
        var params = {
            class:          'User',
            method:         'store',
            id:             id,
            name:           $('#usr-name').val(),
            lastname:       $('#usr-lastname').val(),
            oldemail:       $('#usr-oldemail').val(),
            email:          $('#usr-email').val(),
            oldpassword:    $('#usr-oldpassword').val(),
            password:       $('#usr-password').val(),
            status:         $('#usr-status').val(),
            related:        $('#usr-related').val(),
            role:           $('#usr-role').val(),
            father:         $('#usr-father').val(),
            access:         $('#usr-access').val(),
            permissions:    1
        };

        $.post(connector, {'params':params}, function(e){
            var data = jQuery.parseJSON(e);
            if(true === data.success)
            {
                // if delete, not update
                if(parseInt($('#usr-status').val()) !== 3)
                {
                    // create params
                    var params = {
                        class:          'Account',
                        method:         'store',
                        id_user:        data.id,
                        company:        $('#usr-company').val(),
                        billing_name:   $('#usr-billing-name').val(),
                        billing_email:  $('#usr-billing-email').val(),
                        tax:            $('#usr-tax').val(),
                        category:       $('#usr-category').val(),
                        country:        $('#usr-country').val(),
                        state:          $('#usr-state').val(),
                        address:        $('#usr-address').val(),
                        zip:            $('#usr-zip').val(),
                        phone:          $('#usr-phone').val()
                    };

                    $.post(connector, {'params':params});   


                    var title_name      = $('#usr-name').val() + ' ' + $('#usr-lastname').val();
                    var company_name    = $('#usr-company').val();
                    $('#usr-title-name').html(title_name);
                    $('#usr-id_user').val(data.id);
                    $('#usr-title-company').html(company_name);

                    var params = {
                        class:          'User',
                        method:         'father',
                        id:             data.id
                    }; 

                    $.post(connector, {'params':params}, function(e){
                    var data = jQuery.parseJSON(e);
                        if(true === data.success)
                        {
                            notification('success', data.message); 
//                            window.location = 'users.php';
                        }
                        else
                        {
                            notification('error', data.message);   
                        }
                    });
                }
                else
                {
                    notification('success', data.message); 
//                    window.location = 'users.php';
                }
            }
            else
            {
                notification('error', data.message);   
            }            
        });
    }
    
    /**
     * Store users
     * 
     * @param {int} id User ID 
     * @return string
     */
    function storeUsers(id)
    {
        // create params depending new (0 value) or edit (id value)
        if(empty(id))
        {
            var params = {
                class:          'User',
                method:         'store',
                id:             id,
                name:           $('#usr-admin-name').val(),
                lastname:       $('#usr-admin-lastname').val(),
                oldemail:       null,
                email:          $('#usr-admin-email').val(),
                oldpassword:    null,
                password:       $('#usr-admin-password').val(),
                status:         1,
                role:           2,
                related:        $('#usr-admin-related').val(),
                father:         $('#usr-admin-father').val(),
                access:         JSON.stringify($('#usr-admin-access').val()),
                permissions:    $('#usr-admin-permissions').val()
            };            
        }
        else
        {
            var params = {
                class:          'User',
                method:         'store',
                id:             id,
                name:           $('#usr-admin-' + id + '-name').val(),
                lastname:       $('#usr-admin-' + id + '-lastname').val(),
                oldemail:       $('#usr-admin-' + id + '-oldemail').val(),
                email:          $('#usr-admin-' + id + '-email').val(),
                oldpassword:    $('#usr-admin-' + id + '-oldpassword').val(),
                password:       $('#usr-admin-' + id + '-password').val(),
                status:         $('#usr-admin-' + id + '-status').val(),
                role:           $('#usr-admin-' + id + '-role').val(),
                related:        $('#usr-admin-' + id + '-related').val(),
                father:         $('#usr-admin-' + id + '-father').val(),
                access:         JSON.stringify($('#usr-admin-' + id + '-access').val()),
                permissions:    $('#usr-admin-' + id + '-permissions').val()
            };            
        }

        $.post(connector, {'params':params}, function(e){
            var data = jQuery.parseJSON(e);
            if(true === data.success)
            {
                $('#users-store').collapse();
                $('#modal-user-admin-' + id).modal('hide');
                $('#usr-admin-name').val('');
                $('#usr-admin-lastname').val('');
                $('#usr-admin-email').val('');
                $('#usr-admin-password').val('');
                $('#usr-admin-access').val('');

                // create params
                var params = {
                    class:      'Users',
                    method:     'users',
                    id:         $('#usr-admin-id_user').val(),
                    id_user:    $('#usr-admin-id_user').val()
                };

                $.post(connector, {'params':params}, function(e){
                    var data = jQuery.parseJSON(e);
                    $('#users').html(data);
                    $("select.js-source-2").select2();
                });            

                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
                notification('success', data.message);   
            }
            else
            {
                notification('error', data.message);   
            }
        });
    }