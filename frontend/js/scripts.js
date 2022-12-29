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
     * Check if it is empty
     * 
     * @return boolean
     */
    function empty(mixed_var) 
    {
        var undef, key, i, len;
        var emptyValues = ['Undefined', 'undefined', null, false, 0, '', '0'];

        for (i = 0, len = emptyValues.length; i < len; i++) {
            if (mixed_var === emptyValues[i]) {
            return true;
            }
        }

        if (typeof mixed_var === 'object') {
            for (key in mixed_var) {
            // TODO: should we check for own properties only?
            //if (mixed_var.hasOwnProperty(key)) {
            return false;
            //}
            }
            return true;
        }

        return false;
    }

    /**
     * Delete report from profile
     * 
     * @return boolean
     */
    function deleteReport() 
    {
        // get data
        var id_rep  = $('#id-report').val();

        // create params
        var params = {
            class:      'Report',
            method:     'delete',
            id:         id_rep
        };

        swal({
            title: "¿Está seguro que quiere eliminar el reporte?",
            text: "El tablero se elomirá de forma permanente",
            type: "warning",
            showCancelButton: true,
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí, borrar"
        },
        function () {
            $.post(connector, {'params':params}, function(e){
                var data = jQuery.parseJSON(e);
                if(true === data.success)
                {
                    $('#report').val('');
                    $('#report-id').val('')
                    notification('success', data.message);   
                }
                else
                {
                    notification('error', data.message);   
                }
            }); 
        }); 
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
                window.location = data.location; 
            }
            else
            {
                notification('error', data.message);   
            }
        }); 
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
     * Resize iFrame
     *  
     * @return string
     */
    function resizeIframe(obj) 
    {
        console.log(obj);
        obj.style.height = obj.contentWindow.document.documentElement.scrollHeight + 'px';
    }

    /**
     * Store user
     *  
     * @param string type 
     * @return string
     */
    function storeUser(type)
    {
        var id          = empty($('#id').val()) ? null : $('#id').val();
        var id_rep      = empty($('#id-report').val()) ? null : $('#id-report').val();
        var dataset_id  = $('#dataset-id').val();
        var report_id   = $('#report-id').val();
        
        // validations
        if (true === empty($('#name').val()) || true === empty($('#lastname').val()) || true === empty($('#email').val()) || true === empty($('#password').val()))
        {
            notification('error', 'Complete todos los campos.');           
            return false;
        }

        if (false === empty(dataset_id) || false === empty(report_id))
        {
            if (true === empty(dataset_id))
            {
                notification('error', 'Ingrese un nombre para el reporte.');           
                return false;
            }

            if (true === empty(report_id))
            {
                notification('error', 'Ingrese un id para el reporte.');           
                return false;
            }
        }
        
        // create params
        var params = {
            class:          'User',
            method:         'store',
            id:             id,
            name:           $('#name').val(),
            lastname:       $('#lastname').val(),
            oldemail:       $('#oldemail').val(),
            email:          $('#email').val(),
            oldpassword:    $('#oldpassword').val(),
            password:       $('#password').val(),
            status:         $('#status').val(),
            role:           $('#role').val()
        };

        $.post(connector, {'params':params}, function(e){
            var data = jQuery.parseJSON(e);
            if(true === data.success)
            {
                $('#resume-name').text(params.name + ' ' + params.lastname);
                $('#resume-email').text(params.email);
                var newstatus   = parseInt(params.status) === 1 ? 'Activo' : 'Inactivo';
                $('#resume-status').text(newstatus);
                var newrole     = parseInt(params.role) === 1 ? 'Admin' : parseInt(params.role) === 2 ? 'User' : 'Superadmin';;
                $('#resume-role').text(newrole);
                $('#resume-role').text(newrole);

                notification('success', data.message);

                // store associated report
                if(type === 'USER')
                {
                    var params_rep = {
                        class:      'Report',
                        method:     'store',
                        id:         id_rep,
                        id_user:    data.id,
                        dataset_id: dataset_id,
                        report_id:  report_id
                    };
    
                    $.post(connector, {'params':params_rep}, function(e){
                        var data = jQuery.parseJSON(e);
                        $('#id-report').val(data.id);
                    });
                }
            }
            else
            {
                notification('error', data.message);   
            }            
        });
    }