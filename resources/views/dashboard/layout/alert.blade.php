
@if (session()->has("true"))
 @section('notify')
   <script>
      toastr['success']('👋 {{ session()->get("true") }}.', '', {
        closeButton: true,
        tapToDismiss: false,
        positionClass: 'toast-bottom-right',
        progressBar: true,
        hideDuration: 9000,
        rtl: window.isRtl
      });
   </script>
 @endsection
 @endif
 @if (session()->has("info"))
 @section('notify')
   <script>
      toastr['info']('👋 {{ session()->get("info") }}.', '', {
         closeButton: true,
         tapToDismiss: false,
         positionClass: 'toast-bottom-right',
         progressBar: true,
         hideDuration: 9000,
         rtl: window.isRtl
       });
   </script>
 @endsection
 @endif
 @if(isset($errors) && count($errors) > 0)
 @section('notify')
   <script>
     @foreach($errors->all() as $error)
         toastr['error']('👋 {{ $error }}.', '', {
            closeButton: true,
            tapToDismiss: false,
            positionClass: 'toast-bottom-right',
            progressBar: true,
            hideDuration: 9000,
            rtl: window.isRtl
          });
     @endforeach
   </script>
 @endsection
 @endif
 @if (session()->has("false"))
   @section('notify')
   <script>
   toastr['warning']('👋 {{ session()->get("false") }}.', '', {
      closeButton: true,
      tapToDismiss: false,
      positionClass: 'toast-bottom-right',
      progressBar: true,
      hideDuration: 9000,
      rtl: window.isRtl
    });
   </script>
 @endsection
 @endif
