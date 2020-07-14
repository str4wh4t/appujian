<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ExceptionHook
{
	public function GlobalExceptionHandler()
	{
		set_exception_handler([
			$this,
			'GlobalExceptions'
		]);
	}
	
	public function GlobalExceptions($exception)
	{
		
		if ($exception instanceof ModelNotFoundException) {

//			 show_404();
			show_error('Halaman yang anda cari tidak ditemukan',404,'Perhatian');
        }
		
//		show_404();


//		$msg = 'Exception of type \'' . get_class($exception) . '\' occurred with Message: ' . $exception->getMessage() . ' in File ' . $exception->getFile() . ' at Line ' . $exception->getLine();
//		$msg .= $exception->getTraceAsString();
//
//		log_message('error', $msg, TRUE);
//		echo $msg;    // for code debugging purpose
		$content = '<blockquote class="blockquote pl-1 border-left-primary border-left-3" style="text-align: left">
            <small>'.
				'<b>Msg : </b>' . $exception->getMessage() . '<br>' .
				'<b>Class : </b>' . get_class($exception) . ' <br>' .
				$exception->getFile() . ' <b>at Line</b> ' . $exception->getLine()
			. '</small>
        </blockquote>';
//		$content = '';
		show_error('Maaf, terjadi kesalahan : ' . $content,500,'Perhatian');
		
	}
}
