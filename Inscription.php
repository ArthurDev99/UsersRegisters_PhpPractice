<div class="row justify-content-center mt-3">
    <div class="col-lg-6">
        <form class="form-data mt-4" id="dataForm">

            <div class="form-group">
                <h2>Formulario de datos de usuario</h2>
            </div>
            <div class="form-group">
                <input type="hidden" name="UserId" id="UserId" value="0">
            </div>
            <div class="form-group">
                Nombre:
                <input type="text" name="Nombre" id="Nombre" class="form-control">
            </div>
            <div class="form-group">
                Apellidos:
                <input type="text" name="Apellidos" id="Apellidos" class="form-control">
            </div>
            <div class="form-group">
                Usuario:
                <input type="text" name="Usuario" id="Usuario" class="form-control">
            </div>
            <div class="form-group">
                Password:
                <input type="password" name="Password" id="Password" class="form-control">
            </div>
            <div class="row">
                <div class="col-6">
                    Foto de perfil <br>
                    <input type="file" accept=".jpg, .png" id="UserPhoto" onchange="captureImage()">
                </div>
                <div class="col-6">
                    <div id="DivPhoto" class="mb-2+ text-center">
                    </div>
                </div>
            </div>
            <div class="text-right">
                <button type="submit" class="btn btn-primary">Registrar Datos</button>

                <button type="button" class="btn btn-info" onclick="cleanForm()">Limpiar</button>

            </div>
        </form>
    </div>

    <div class="col-lg-6 mt-4">
        <table class="table limit-height" id="tableUsers">
            <thead class="thead-dark text-center">
                <tr>
                    <th>
                        Nombre
                    </th>

                    <th>
                        Apellido
                    </th>

                    <th>
                        Usuario
                    </th>

                    <th>
                        Actions
                    </th>
                </tr>
            </thead>

            <tbody>
                
            </tbody>

        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-1.12.4.js" integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU=" crossorigin="anonymous"></script>
<script src="/Scripts/MyScripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>