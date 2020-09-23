<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="./assets/css/style.css">
    <title>Asistencia culto</title>
</head>

<body>
    <div class="row w-100 h-100 container-fluid justify-content-center align-content-center text-center" id="main-container">
        <form action="">
            <h1 class="text-center title" style="text-transform: uppercase;">Asistencia a cultos</h1>
            <h2 class="text-center">Este formulario es con el fin de organizar la asistencia presencial a los cultos, la cual está limitada por la reglamentación de distanciamiento social para evitar congestión y que usted no pueda ingresar al culto por falta de cupo.</h2>
            <div class="form-group row">
                <div class="col-md-6 form-space">
                    <label for="name">Ingrese su nombre</label>
                    <input type="text" class="form-control" id="name" required>
                </div>
                <div class="col-md-6">
                    <label for="id">Ingrese su número de documento</label>
                    <input type="number" class="form-control" id="id" required>
                </div>
            </div>
            <div class="form-group row justify-content-center align-items-center">
                <div class="col-md-6 form-space">
                    <label for="culto">¿A que hora desea asistir?</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exampleRadios" id="culto8" value="culto8">
                        <label class="form-check-label" for="culto8">
                                8:00am-9:00am
                            </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exampleRadios" id="culto10" value="culto10">
                        <label class="form-check-label" for="culto10">
                                10:00am-11:00am
                            </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exampleRadios" id="culto12" value="culto12">
                        <label class="form-check-label" for="culto12">
                                12:00m-1:00pm
                            </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exampleRadios" id="culto5" value="option2">
                        <label class="form-check-label" for="culto5">
                                5:00pm-6:00pm
                            </label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="people">¿Cuántas personas van a asistir? (Contándose a usted)</label>
                        <input type="number" name="people" class="form-control">
                        <small id="peopleHelp" class="form-text text-muted">Porfavor verifique que alguien no haya registrado ya su cupo</small>
                    </div>
                    <div class="form-group">
                        <label for="phone">Telefono de contacto</label>
                        <input type="number" name="phone" class="form-control">
                    </div>
                </div>
            </div>
            <input type="submit" value="✔ Enviar" class="btn btn-primary" name="submit">
        </form>
    </div>
</body>

</html>