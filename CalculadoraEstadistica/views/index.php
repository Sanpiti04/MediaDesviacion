<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Calculadora Estadística Avanzada</title>
    <style>
        /* CSS para cumplir el REQUISITO DE USABILIDAD (Interfaz intuitiva) */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 30px;
            background-color: #f4f4f9;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        .config-box,
        .results-box {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .error {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
        }

        .info {
            color: #004085;
            background-color: #cce5ff;
            border: 1px solid #b8daff;
            padding: 10px;
            border-radius: 5px;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
        }

        button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>📊 Calculadora de Media y Desviación Estándar</h1>

        <?php if (isset($error_message)): ?>
            <p class="error">⚠️ **Error del Sistema:** <?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <div class="config-box">
            <h2>Paso 1: Configuración de Base de Datos y Selección</h2>
            <form id="calculatorForm" method="POST" action="index.php?action=calcular">

                <label for="driver">Motor de Base de Datos (Compatibilidad):</label>
                <select name="driver" id="driver">
                    <option value="mysql" <?= ($this->driver === 'mysql' ? 'selected' : '') ?>>MySQL</option>
                    <option value="pgsql" <?= ($this->driver === 'pgsql' ? 'selected' : '') ?>>PostgreSQL</option>
                    <option value="sqlsrv" <?= ($this->driver === 'sqlsrv' ? 'selected' : '') ?>>SQL Server</option>
                </select>

                <label for="table">Tabla de la Base de Datos:</label>
                <select name="table" id="table" required onchange="this.form.submit()">
                    <option value="">-- Seleccione la Tabla --</option>
                    <?php
                    // Se utiliza $selected_table para mantener la opción seleccionada después del submit.
                    if (is_array($tables) && !isset($tables['error'])) {
                        foreach ($tables as $t) {
                            $selected = (isset($selected_table) && $selected_table === $t) ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($t) . "' $selected>" . htmlspecialchars($t) . "</option>";
                        }
                    } else if (isset($tables['error'])) {
                        echo "<option value='' disabled>" . $tables['error'] . "</option>";
                    }
                    ?>
                </select>

                <p style="font-size: small; color: #555;">*Al seleccionar una tabla, la página se recarga para cargar las columnas disponibles.</p>

                <label for="column">Columna con Valores Numéricos (REQUISITO DE USABILIDAD):</label>
                <select name="column" id="column" required>
                    <option value="">-- Seleccione la Columna --</option>
                    <?php
                    if (isset($columns) && is_array($columns) && !isset($columns['error'])) {
                        // Iteramos sobre las columnas devueltas por el Controlador
                        foreach ($columns as $c) {
                            $selected = (isset($selected_column) && $selected_column === $c) ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($c) . "' $selected>" . htmlspecialchars($c) . "</option>";
                        }
                    } else if (isset($columns['error'])) {
                        echo "<option value='' disabled>" . $columns['error'] . "</option>";
                    } else {
                        echo "<option value='' disabled>Seleccione primero una tabla</option>";
                    }
                    ?>
                </select>

                <button type="submit">Calcular Estadísticas</button>

                <div id="loadingMessage" style="color: #007bff; margin-top: 15px; display: none;">
                    Calculando, por favor espere... ⏳
                </div>

                <script>
                    function showLoading() {
                        document.getElementById('loadingMessage').style.display = 'block';
                    }
                </script>

                
            </form>
        </div>

        <?php if (isset($resultados)): ?>
            <div class="results-box">
                <h2>✅ Resultados del Análisis</h2>
                <table>
                    <tr>
                        <th>Estadística</th>
                        <th>Fórmula</th>
                        <th>Valor (Precisión de 6 decimales)</th>
                    </tr>
                    <tr>
                        <td>**Media Aritmética ($\mu$)**</td>
                        <td>$\mu = (\Sigma x_i) / n$</td>
                        <td><?= htmlspecialchars($resultados['media']) ?></td>
                    </tr>
                    <tr>
                        <td>**Desviación Estándar ($\sigma$)**</td>
                        <td>$\sigma = \sqrt{[\Sigma(x_i - \mu)² / n]}$</td>
                        <td><?= htmlspecialchars($resultados['desviacion_estandar']) ?></td>
                    </tr>
                </table>

                <h3>Estadísticas Básicas</h3>
                <div class="info">
                    <p>Datos Procesados: **<?= htmlspecialchars($resultados['num_procesados']) ?>**</p>
                    <p>Valor Mínimo: <?= htmlspecialchars($resultados['minimo']) ?></p>
                    <p>Valor Máximo: <?= htmlspecialchars($resultados['maximo']) ?></p>
                    <p>Rango (Máximo - Mínimo): <?= htmlspecialchars($resultados['rango']) ?></p>
                    <p style="font-size: small;">Tiempo de Procesamiento de Datos: **<?= number_format($resultados['tiempo_procesamiento'], 4) ?> segundos**</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>