<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$etiquetas=array('borrador'=>array('Borrador','secondary'),'publicado'=>array('Pendiente de revisión','info'),'aprobado'=>array('Aprobada','success'),'rechazado'=>array('Necesita corrección','warning'),'sin_registrar'=>array('Sin registrar','light'));
$etiqueta=$etiquetas[$estado]??array('Sin registrar','light');
?><span class="badge badge-<?= $etiqueta[1] ?>"><?= $etiqueta[0] ?></span>
