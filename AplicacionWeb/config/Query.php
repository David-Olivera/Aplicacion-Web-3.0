<? php
/ **
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
 * /
namespace  yii \ db ;
usa  Yii ;
use  yii \ base \ Component ;
use  yii \ base \ InvalidArgumentException ;
use  yii \ helpers \ ArrayHelper ;
use  yii \ base \ InvalidConfigException ;
/ **
 * Query representa una instrucción SELECT SQL de una manera que es independiente de DBMS.
 *
 * Query proporciona un conjunto de métodos para facilitar la especificación de diferentes cláusulas
* en una declaración SELECT. Estos métodos se pueden encadenar juntos.
 *
 * Al llamar a [[createCommand ()]], podemos obtener una instancia de [[Command]] que puede estar más allá
 * utilizado para realizar / ejecutar la consulta DB contra una base de datos.
 *
 * Por ejemplo,
 *
 * `` `php
 * $ consulta = nueva consulta;
 * // redacta la consulta
 * $ query-> select ('id, nombre')
 * -> desde ('usuario')
 * -> límite (10);
 * // construir y ejecutar la consulta
 * $ rows = $ query-> all ();
 * // alternativamente, puede crear un comando DB y ejecutarlo
 * $ command = $ query-> createCommand ();
 * // $ command-> sql devuelve el SQL real
 * $ rows = $ command-> queryAll ();
 * `` `
 *
 * Query utiliza internamente la clase [[QueryBuilder]] para generar la instrucción SQL.
 *
 * Puede encontrar una guía de uso más detallada sobre cómo trabajar con Query en el [artículo de guía sobre Query Builder] (guía: db-query-builder).
 *
* @property string [] $ tablesUsedInFrom Nombres de tabla indexados por alias. Esta propiedad es de sólo lectura.
 *
* @author Qiang Xue <qiang.xue@gmail.com>
* @author Carsten Brandt <mail@cebe.cc>
* @since 2.0
 * /
clase  Query  extends  Component  implementa  QueryInterface , ExpressionInterface
{
    usar  QueryTrait ;
    / **
     * @var array las columnas que se seleccionan. Por ejemplo, `['id', 'name']`.
     * Esto se usa para construir la cláusula SELECT en una declaración de SQL. Si no está configurado, significa seleccionar todas las columnas.
     * @see seleccionar ()
     * /
    public  $ select ;
    / **
     * @var cadena opción adicional que se debe agregar a la palabra clave 'SELECCIONAR'. Por ejemplo,
     * en MySQL, se puede usar la opción 'SQL_CALC_FOUND_ROWS'.
     * /
    public  $ selectOption ;
    / **
     * @var bool si se seleccionan distintas filas de datos solamente. Si esto se establece verdadero,
     * la cláusula SELECT se cambiaría a SELECT DISTINCT.
     * /
    público  $ distinct ;
    / **
     * @var array de la (s) tabla (s) a seleccionar desde. Por ejemplo, `['usuario', 'publicar']`.
     * Esto se usa para construir la cláusula FROM en una declaración de SQL.
     * @see desde ()
     * /
    público  $ de ;
    / **
     * @var array cómo agrupar los resultados de la consulta. Por ejemplo, `['compañía', 'departamento']`.
     * Esto se usa para construir la cláusula GROUP BY en una declaración SQL.
     * /
    public  $ groupBy ;
    / **
     * @var array cómo unirse a otras tablas. Cada elemento de matriz representa la especificación
     * de una unión que tiene la siguiente estructura:
     *
     * `` `php
     * [$ joinType, $ tableName, $ joinCondition]
     * `` `
     *
     * Por ejemplo,
     *
     * `` `php
     * [
     * ['' INNER JOIN ',' user ',' user.id = author_id '],
     * ['LEFT JOIN', 'team', 'team.id = team_id'],
     *]
     * `` `
     * /
    público  $ unirse ;
    / **
     * @var string | array | ExpressionInterface la condición que se aplicará en la cláusula GROUP BY.
     * Puede ser una cadena o una matriz. Consulte [[where ()]] sobre cómo especificar la condición.
     * /
    público  $ teniendo ;
    / **
     * @var array esto se usa para construir la cláusula UNION en una declaración SQL.
     * Cada elemento de matriz es una matriz de la siguiente estructura:
     *
     * - `query`: una cadena o un objeto [[Query]] que representa una consulta
     * - `all`: boolean, si debería ser` UNION ALL` o `UNION`
     * /
    público  $ union ;
    / **
     * @var lista de matriz de valores de parámetros de consulta indexados por marcadores de posición de parámetros.
     * Por ejemplo, `[': name' => 'Dan', ': age' => 31]`.
     * /
    public  $ params  = [];
    / **
     * @var int | true el número predeterminado de segundos que los resultados de la consulta pueden seguir siendo válidos en la memoria caché.
     * Use 0 para indicar que los datos en caché nunca caducarán.
     * Use un número negativo para indicar que la caché de consultas no debe ser utilizada.
     * Use boolean `true` para indicar que se debe usar [[Connection :: queryCacheDuration]].
     * @see cache ()
     * @since 2.0.14
     * /
    public  $ queryCacheDuration ;
    / **
     * @var \ yii \ caching \ Dependency la dependencia a asociar con el resultado de la consulta en caché para esta consulta
     * @see cache ()
     * @since 2.0.14
     * /
    public  $ queryCacheDependency ;
    / **
     * Crea un comando DB que se puede usar para ejecutar esta consulta.
     * @param Connection $ db la conexión de base de datos utilizada para generar la instrucción SQL.
     * Si no se proporciona este parámetro, se usará el componente de aplicación `db`.
     * @return Comando la instancia del comando DB creado.
     * /
     función  pública createCommand ( $ db  =  null )
    {
        if ( $ db  ===  null ) {
            $ db  =  Yii :: $ app -> getDb ();
        }
        list ( $ sql , $ params ) =  $ db -> getQueryBuilder () -> build ( $ this );
        $ command  =  $ db -> createCommand ( $ sql , $ params );
        $ this -> setCommandCache ( $ command );
        return  $ command ;
    }
    / **
     * Se prepara para construir SQL.
     * Este método es llamado por [[QueryBuilder]] cuando comienza a generar SQL desde un objeto de consulta.
     * Puede anular este método para realizar un trabajo final de preparación al convertir una consulta en una declaración SQL.
     * @param QueryBuilder $ builder
     * @return $ this una instancia de consulta preparada que será utilizada por [[QueryBuilder]] para construir el SQL
     * /
     preparar la función  pública ( $ builder )
    {
        devuelve  $ this ;
    }
    / **
     * Inicia una consulta por lotes.
     *
     * Una consulta por lotes admite la recuperación de datos en lotes, lo que puede mantener el uso de la memoria por debajo de un límite.
     * Este método devolverá un objeto [[BatchQueryResult]] que implementa la interfaz [[\ Iterator]]
     * y puede atravesarse para recuperar los datos en lotes.
     *
     * Por ejemplo,
     *
     * `` `php
     * $ query = (nueva consulta) -> desde ('usuario');
     * foreach ($ query-> batch () as $ rows) {
     * // $ rows es una matriz de 100 filas o menos desde la tabla de usuario
     *}
     * `` `
     *
     * @param int $ batchSize la cantidad de registros que se deben obtener en cada lote.
     * @param Connection $ db la conexión de la base de datos. Si no se establece, se usará el componente de aplicación "db".
     * @return BatchQueryResult el resultado de la consulta por lotes. Implementa la interfaz [[\ Iterator]]
     * y puede atravesarse para recuperar los datos en lotes.
     * /
     lote de función  pública ( $ batchSize = 100 , $ db = null )    
    {
        devuelve  Yii :: createObject ([
            ' class '  =>  BatchQueryResult :: className (),
            ' query '  =>  $ this ,
            ' batchSize '  =>  $ batchSize ,
            ' db '  =>  $ db ,
            ' cada '  =>  falso ,
        ]);
    }
    / **
     * Inicia una consulta por lotes y recupera datos fila por fila.
     *
     * Este método es similar a [[batch ()]] excepto que en cada iteración del resultado,
     * solo se devuelve una fila de datos. Por ejemplo,
     *
     * `` `php
     * $ query = (nueva consulta) -> desde ('usuario');
     * foreach ($ query-> each () as $ row) {
     *}
     * `` `
     *
     * @param int $ batchSize la cantidad de registros que se deben obtener en cada lote.
     * @param Connection $ db la conexión de la base de datos. Si no se establece, se usará el componente de aplicación "db".
     * @return BatchQueryResult el resultado de la consulta por lotes. Implementa la interfaz [[\ Iterator]]
     * y puede atravesarse para recuperar los datos en lotes.
     * /
     función  pública cada ( $ batchSize  =  100 , $ db  =  null )
    {
        devuelve  Yii :: createObject ([
            ' class '  =>  BatchQueryResult :: className (),
            ' query '  =>  $ this ,
            ' batchSize '  =>  $ batchSize ,
            ' db '  =>  $ db ,
            ' each '  =>  verdadero ,
        ]);
    }
    / **
     * Ejecuta la consulta y devuelve todos los resultados como una matriz.
     * @param Connection $ db la conexión de base de datos utilizada para generar la instrucción SQL.
     * Si no se proporciona este parámetro, se usará el componente de aplicación `db`.
     * @return array los resultados de la consulta. Si la consulta no genera nada, se devolverá una matriz vacía.
     * /
     función  pública todo ( $ db  =  null )
    {
        if ( $ this -> emulateExecution ) {
            devolver [];
        }
        $ rows  =  $ this -> createCommand ( $ db ) -> queryAll ();
        devuelve  $ this -> populate ( $ rows );
    }
    / **
     * Convierte los resultados de la consulta en bruto en el formato especificado por esta consulta.
     * Este método se usa internamente para convertir los datos obtenidos de la base de datos
     * en el formato requerido por esta consulta.
     * @param array $ filas el resultado de la consulta sin formato de la base de datos
     * @return array el resultado de la consulta convertida
     * /
     función  pública populate ( $ rows )
    {
        if ( $ this -> indexBy  ===  null ) {
            devuelve  $ filas ;
        }
        $ result  = [];
        foreach ( $ rows  as  $ row ) {
            $ result [ ArrayHelper :: getValue ( $ row , $ this -> indexBy )] =  $ fila ;
        }
        devolver  $ resultado ;
    }
    / **
     * Ejecuta la consulta y devuelve una sola fila de resultados.
     * @param Connection $ db la conexión de base de datos utilizada para generar la instrucción SQL.
     * Si no se proporciona este parámetro, se usará el componente de aplicación `db`.
     * @return array | bool la primera fila (en términos de una matriz) del resultado de la consulta. False se devuelve si la consulta
     * no genera nada
     * /
     función  pública uno ( $ db  =  null )
    {
        if ( $ this -> emulateExecution ) {
            volver  falsa ;
        }
        devuelve  $ this -> createCommand ( $ db ) -> queryOne ();
    }
    / **
     * Devuelve el resultado de la consulta como un valor escalar.
     * El valor devuelto será la primera columna en la primera fila de los resultados de la consulta.
     * @param Connection $ db la conexión de base de datos utilizada para generar la instrucción SQL.
     * Si no se proporciona este parámetro, se usará el componente de aplicación `db`.
     * @return string | null | false el valor de la primera columna en la primera fila del resultado de la consulta.
     * False se devuelve si el resultado de la consulta está vacío.
     * /
     función  pública escalar ( $ db  =  null )
    {
        if ( $ this -> emulateExecution ) {
            devolver  nulo ;
        }
        devuelve  $ this -> createCommand ( $ db ) -> queryScalar ();
    }
    / **
     * Ejecuta la consulta y devuelve la primera columna del resultado.
     * @param Connection $ db la conexión de base de datos utilizada para generar la instrucción SQL.
     * Si no se proporciona este parámetro, se usará el componente de aplicación `db`.
     * @return array la primera columna del resultado de la consulta. Se devuelve una matriz vacía si la consulta no genera nada.
     * /
     columna de función  pública ( $ db = null )  
    {
        if ( $ this -> emulateExecution ) {
            devolver [];
        }
        if ( $ this -> indexBy  ===  null ) {
            devuelve  $ this -> createCommand ( $ db ) -> queryColumn ();
        }
        if ( is_string ( $ this -> indexBy ) &&  is_array ( $ this -> select ) &&  count ( $ this -> select ) ===  1 ) {
            if ( strpos ( $ this -> indexBy , ' . ' ) ===  false  &&  count ( $ tables  =  $ this -> getTablesUsedInFrom ()) >  0 ) {
                $ this -> select [] =  key ( $ tables ) .  ' . '  .  $ this -> indexBy ;
            } else {
                $ this -> select [] =  $ this -> indexBy ;
            }
        }
        $ rows  =  $ this -> createCommand ( $ db ) -> queryAll ();
        $ resultados  = [];
        foreach ( $ rows  as  $ row ) {
            $ value  =  reset ( $ row );
            if ( $ this -> indexBy  instanceof  \ Closure ) {
                $ resultado [ call_user_func ( $ this -> indexBy , $ fila )] =  $ valor ;
            } else {
                $ results [ $ row [ $ this -> indexBy ]] =  $ value ;
            }
        }
        devolver  $ resultados ;
    }
    / **
     * Devuelve la cantidad de registros.
     * @param string $ q la expresión COUNT. El valor predeterminado es '*'.
     * Asegúrese de nombrar correctamente los nombres de las columnas [quote] (guide: db-dao # quoting-table-and-column-names) en la expresión.
     * @param Connection $ db la conexión de base de datos utilizada para generar la instrucción SQL.
     * Si no se proporciona este parámetro (o nulo), se usará el componente de aplicación `db`.
     * @return int | string cantidad de registros. El resultado puede ser una cadena dependiendo del
     * motor de base de datos subyacente y para admitir valores enteros superiores a los que puede manejar un entero PHP de 32 bits.
     * /
     recuento de funciones  públicas ( $ q = ' * ' , $ db = null )    
    {
        if ( $ this -> emulateExecution ) {
            return  0 ;
        }
        devuelve  $ this -> queryScalar ( " COUNT ( $ q ) " , $ db );
    }
    / **
     * Devuelve la suma de los valores de columna especificados.
     * @param string $ q el nombre o la expresión de la columna.
     * Asegúrese de nombrar correctamente los nombres de las columnas [quote] (guide: db-dao # quoting-table-and-column-names) en la expresión.
     * @param Connection $ db la conexión de base de datos utilizada para generar la instrucción SQL.
     * Si no se proporciona este parámetro, se usará el componente de aplicación `db`.
     * @return mezcló la suma de los valores de columna especificados.
     * /
     suma de función  pública ( $ q , $ db = null )  
    {
        if ( $ this -> emulateExecution ) {
            return  0 ;
        }
        return  $ this -> queryScalar ( " SUM ( $ q ) " , $ db );
    }
    / **
     * Devuelve el promedio de los valores de columna especificados.
     * @param string $ q el nombre o la expresión de la columna.
     * Asegúrese de nombrar correctamente los nombres de las columnas [quote] (guide: db-dao # quoting-table-and-column-names) en la expresión.
     * @param Connection $ db la conexión de base de datos utilizada para generar la instrucción SQL.
     * Si no se proporciona este parámetro, se usará el componente de aplicación `db`.
     * @return mezcló el promedio de los valores de columna especificados.
     * /
     promedio de la función  pública ( $ q , $ db = null )  
    {
        if ( $ this -> emulateExecution ) {
            return  0 ;
        }
        return  $ this -> queryScalar ( " AVG ( $ q ) " , $ db );
    }
    / **
     * Devuelve el mínimo de los valores de columna especificados.
     * @param string $ q el nombre o la expresión de la columna.
     * Asegúrese de nombrar correctamente los nombres de las columnas [quote] (guide: db-dao # quoting-table-and-column-names) en la expresión.
     * @param Connection $ db la conexión de base de datos utilizada para generar la instrucción SQL.
     * Si no se proporciona este parámetro, se usará el componente de aplicación `db`.
     * @return mezcló el mínimo de los valores de columna especificados.
     * /
     función  pública min ( $ q , $ db  =  null )
    {
        devuelve  $ this -> queryScalar ( " MIN ( $ q ) " , $ db );
    }
    / **
     * Devuelve el máximo de los valores de columna especificados.
     * @param string $ q el nombre o la expresión de la columna.
     * Asegúrese de nombrar correctamente los nombres de las columnas [quote] (guide: db-dao # quoting-table-and-column-names) en la expresión.
     * @param Connection $ db la conexión de base de datos utilizada para generar la instrucción SQL.
     * Si no se proporciona este parámetro, se usará el componente de aplicación `db`.
     * @return mezcló el máximo de los valores de columna especificados.
     * /
     Función  pública max ( $ q , $ db  =  null )
    {
        devuelve  $ this -> queryScalar ( " MAX ( $ q ) " , $ db );
    }
    / **
     * Devuelve un valor que indica si el resultado de la consulta contiene alguna fila de datos.
     * @param Connection $ db la conexión de base de datos utilizada para generar la instrucción SQL.
     * Si no se proporciona este parámetro, se usará el componente de aplicación `db`.
     * @return bool si el resultado de la consulta contiene alguna fila de datos.
     * /
     función  pública existe ( $ db  =  null )
    {
        if ( $ this -> emulateExecution ) {
            volver  falsa ;
        }
        $ command  =  $ this -> createCommand ( $ db );
        $ params  =  $ command -> params ;
        $ comando -> setSql ( $ comando -> db -> getQueryBuilder () -> selectExists ( $ comando -> getSql ()));
        $ command -> bindValues ​​( $ params );
        return ( bool ) $ command -> queryScalar ();
    }
    / **
     * Consulta un valor escalar configurando [[seleccionar]] primero.
     * Restaura el valor de select para que esta consulta sea reutilizable.
     * @param string | ExpressionInterface $ selectExpression
     * @param Connection | null $ db
     * @return bool | cadena
     * /
     función  protegida queryScalar ( $ selectExpression , $ db )
    {
        if ( $ this -> emulateExecution ) {
            devolver  nulo ;
        }
        si (
            ! $ this -> distinct
            &&  empty ( $ this -> groupBy )
            &&  empty ( $ this -> having )
            &&  empty ( $ this -> union )
        ) {
            $ select  =  $ this -> select ;
            $ order  =  $ this -> orderBy ;
            $ limit  =  $ this -> limit ;
            $ offset  =  $ this -> offset ;
            $ this -> select  = [ $ selectExpression ];
            $ this -> orderBy  =  null ;
            $ this -> limit  =  null ;
            $ this -> offset  =  null ;
            $ command  =  $ this -> createCommand ( $ db );
            $ this -> select  =  $ select ;
            $ this -> orderBy  =  $ order ;
            $ this -> limit  =  $ limit ;
            $ this -> offset  =  $ offset ;
            return  $ command -> queryScalar ();
        }
        $ command  = ( nuevo  self ())
            -> seleccionar ([ $ selectExpression ])
            -> desde ([ ' c '  =>  $ this ])
            -> createCommand ( $ db );
        $ this -> setCommandCache ( $ command );
        return  $ command -> queryScalar ();
    }
    / **
     * Devuelve nombres de tabla utilizados en [[desde]] indexados por alias.
     * Ambos alias y nombres están encerrados en {{y}}.
     * @return string [] nombres de tabla indexados por alias
     * @throws \ yü \ Base \ InvalidConfigException
     * @ desde 2.0.12
     * /
     función  pública getTablesUsedInFrom ()
    {
        if ( vacío ( $ this -> from )) {
            devolver [];
        }
        if ( is_array ( $ this -> from )) {
            $ tableNames  =  $ this -> from ;
        } elseif ( is_string ( $ this -> from )) {
            $ tableNames  =  preg_split ( '/ \ s * , \ s * /' , trim ( $ this -> from ), - 1 , PREG_SPLIT_NO_EMPTY );
        } elseif ( $ this -> from  instanceof  Expression ) {
            $ tableNames  = [ $ this -> from ];
        } else {
            lanzar  nueva  InvalidConfigException ( gettype ( $ this -> from ) .  ' in $ from no es compatible. ' );
        }
        devuelve  $ this -> cleanUpTableNames ( $ tableNames );
    }
    / **
     * Limpiar nombres de tablas y alias
     * Ambos alias y nombres están encerrados en {{y}}.
     * @param array $ tableNames matriz no vacía
     * @return string [] nombres de tabla indexados por alias
     * @since 2.0.14
     * /
     función  protegida cleanUpTableNames ( $ tableNames )
    {
        $ cleanedUpTableNames  = [];
        foreach ( $ tableNames  as  $ alias  =>  $ tableName ) {
            if ( is_string ( $ tableName ) &&  ! is_string ( $ alias )) {
                $ patrón  =  <<< PATRÓN
~
^
\ s *
(
(?: ['"` \ [] | {{)
. *?
(?: ['"` \]] |}})
|
\ (. *? \)
|
. *?
)
(?
(?
    \ s +
    (?:como)?
    \ s *
)
(
   (?: ['"` \ [] | {{)
    . *?
    (?: ['"` \]] |}})
    |
    . *?
)
)?
\ s *
ps
~ iux
PATRÓN ;
                if ( preg_match ( $ patrón , $ tableName , $ coincidencias )) {
                    if ( isset ( $ matches [ 2 ])) {
                        list (, $ tableName , $ alias ) =  $ coincidencias ;
                    } else {
                        $ tableName  =  $ alias  =  $ coincidencias [ 1 ];
                    }
                }
            }
            if ( $ tableName  instanceof  Expression ) {
                if ( ! is_string ( $ alias )) {
                    throw  new  InvalidArgumentException ( ' Para usar Expression in from () método, páselo en formato de matriz con alias. ' );
                }
                $ cleanedUpTableNames [ $ this -> ensureNameQuoted ( $ alias )] =  $ tableName ;
            } elseif ( $ tableName  instanceof  self ) {
                $ cleanedUpTableNames [ $ this -> ensureNameQuoted ( $ alias )] =  $ tableName ;
            } else {
                $ cleanedUpTableNames [ $ this -> ensureNameQuoted ( $ alias )] =  $ this -> ensureNameQuoted ( $ tableName );
            }
        }
        return  $ cleanedUpTableNames ;
    }
    / **
     * Asegura que el nombre esté envuelto con {{y}}
     * @param string $ nombre
     * @return string
     * /
     función  privada ensureNameQuoted ( $ name )
    {
        $ name  =  str_replace ([ " ' " , ' " ' , ' ` ' , ' [ ' , ' ] ' ], ' ' , $ nombre );
        if ( $ nombre  &&  ! preg_match ( '/ ^ {{. * }} $ /' , $ nombre )) {
            devolver  ' {{ '  .  $ nombre  .  ' }} ' ;
        }
        devolver  $ nombre ;
    }
    / **
     * Establece la parte SELECCIONAR de la consulta.
     * @param string | array | ExpressionInterface $ columnas las columnas que se seleccionarán.
     * Las columnas se pueden especificar en una cadena (por ejemplo, "id, name") o en una matriz (por ejemplo, ['id', 'name']).
     * Las columnas pueden tener un prefijo con nombres de tabla (por ejemplo, "user.id") y / o contener alias de columna (por ejemplo, "user.id AS user_id").
     * El método citará automáticamente los nombres de las columnas a menos que una columna contenga algunos paréntesis
     * (lo que significa que la columna contiene una expresión DB). Una expresión DB también se puede pasar en forma de
     * un objeto [[ExpressionInterface]].
     *
     * Tenga en cuenta que si está seleccionando una expresión como `CONCAT (first_name, '', last_name)`, debería
     * use una matriz para especificar las columnas. De lo contrario, la expresión puede dividirse incorrectamente en varias partes.
     *
     * Cuando las columnas se especifican como una matriz, también puede usar las teclas de matriz como los alias de la columna (si es una columna)
     * no necesita alias, no use una clave de cadena).
     *
     * A partir de la versión 2.0.1, también puede seleccionar subconsultas como columnas especificando cada columna
     * como una instancia `Query` que representa la subconsulta.
     *
     * @param string $ option opción adicional que se debe anexar a la palabra clave 'SELECT'. Por ejemplo,
     * en MySQL, se puede usar la opción 'SQL_CALC_FOUND_ROWS'.
     * @return $ this el objeto de consulta en sí
     * /
     selección de función  pública ( $ columns , $ option = null )  
    {
        if ( $ columns  instanceof  ExpressionInterface ) {
            $ columns  = [ $ columns ];
        } elseif ( ! is_array ( $ columns )) {
            $ columns  =  preg_split ( '/ \ s * , \ s * /' , trim ( $ columns ), - 1 , PREG_SPLIT_NO_EMPTY );
        }
        // esta asignación sequantial es necesaria para asegurarse de que se está restableciendo la selección
        // antes de usar getUniqueColumns () que lo verifica
        $ this -> select  = [];
        $ this -> select  =  $ this -> getUniqueColumns ( $ columns );
        $ this -> selectionOption  =  $ option ;
        devuelve  $ this ;
    }
    / **
     * Agregue más columnas a la parte SELECCIONAR de la consulta.
     *
     * Tenga en cuenta que si [[seleccionar]] no se ha especificado antes, debe incluir `*` explícitamente
     * si desea seleccionar todas las columnas restantes también:
     *
     * `` `php
     * $ query-> addSelect (["*", "CONCAT (first_name, '', last_name) AS full_name"]) -> one ();
     * `` `
     *
     * @param string | matriz | ExpressionInterface $ columnas las columnas para agregar a la selección. Ver [[select ()]] para más
     * detalles sobre el formato de este parámetro.
     * @return $ this el objeto de consulta en sí
     * @see seleccionar ()
     * /
     función  pública addSelect ( $ columns )
    {
        if ( $ columns  instanceof  ExpressionInterface ) {
            $ columns  = [ $ columns ];
        } elseif ( ! is_array ( $ columns )) {
            $ columns  =  preg_split ( '/ \ s * , \ s * /' , trim ( $ columns ), - 1 , PREG_SPLIT_NO_EMPTY );
        }
        $ columns  =  $ this -> getUniqueColumns ( $ columns );
        if ( $ this -> select  ===  null ) {
            $ this -> select  =  $ columns ;
        } else {
            $ this -> select  =  array_merge ( $ this -> select , $ columns );
        }
        devuelve  $ this ;
    }
    / **
     * Devuelve nombres únicos de columnas excluyendo duplicados.
     * Columnas a eliminar:
     * - si la definición de columna ya está presente en la parte SELECCIONAR con el mismo alias
     * - si la definición de columna sin alias ya está presente en SELECCIONAR parte sin alias también
     * @param array $ columns las columnas que se fusionarán con la selección.
     * @since 2.0.14
     * /
     función  protegida getUniqueColumns ( $ columns )
    {
        $ unaliasedColumns  =  $ this -> getUnaliasedColumnsFromSelect ();
        $ result  = [];
        foreach ( $ columns  as  $ columnAlias  =>  $ columnDefinition ) {
            if ( ! $ columnDefinition  instanceof  Query ) {
                if ( is_string ( $ columnAlias )) {
                    $ existsInSelect  =  isset ( $ this -> select [ $ columnAlias ]) &&  $ this -> select [ $ columnAlias ] ===  $ columnDefinition ;
                    if ( $ existsInSelect ) {
                        continuar ;
                    }
                } elseif ( is_int ( $ columnAlias )) {
                    $ existsInSelect  =  in_array ( $ columnDefinition , $ unaliasedColumns , true );
                    $ existsInResultSet  =  in_array ( $ columnDefinition , $ result , true );
                    if ( $ existsInSelect  ||  $ existsInResultSet ) {
                        continuar ;
                    }
                }
            }
            $ result [ $ columnAlias ] =  $ columnDefinition ;
        }
        devolver  $ resultado ;
    }
    / **
     * @return array Lista de columnas sin alias de la instrucción SELECT.
     * @since 2.0.14
     * /
     función  protegida getUnaliasedColumnsFromSelect ()
    {
        $ result  = [];
        if ( is_array ( $ this -> select )) {
            foreach ( $ this -> select  as  $ name  =>  $ value ) {
                if ( is_int ( $ nombre )) {
                    $ result [] =  $ value ;
                }
            }
        }
        return  array_unique ( $ result );
    }
    / **
     * Establece el valor que indica si SELECCIONAR DISTINCT o no.
     * @param bool $ valora si SELECCIONAR DISTINCT o no.
     * @return $ this el objeto de consulta en sí
     * /
     función  pública distinta ( $ value  =  true )
    {
        $ this -> distinct  =  $ value ;
        devuelve  $ this ;
    }
    / **
     * Establece la parte FROM de la consulta.
     * @param string | array | ExpressionInterface $ tablas de las tablas para seleccionar. Esto puede ser una cadena (por ej. `` User'`)
     * o una matriz (por ejemplo, `['usuario', 'perfil']`) que especifica uno o varios nombres de tabla.
     * Los nombres de tabla pueden contener prefijos de esquema (p. Ej., `'Public.user'`) y / o alias de tabla (p. Ej.`' Usuario u'`).
     * El método citará automáticamente los nombres de la tabla a menos que contenga algunos paréntesis
     * (lo que significa que la tabla se da como una subconsulta o expresión DB).
     *
     * Cuando las tablas se especifican como una matriz, también puede usar las claves de la matriz como los alias de la tabla
     * (si una tabla no necesita alias, no use una clave de cadena).
     *
     * Use un objeto Query para representar una subconsulta. En este caso, se usará la clave de matriz correspondiente
     * como el alias de la subconsulta.
     *
     * Para especificar la parte `FROM` en SQL simple, puede pasar una instancia de [[ExpressionInterface]].
     *
     * Aquí hay unos ejemplos:
     *
     * `` `php
     * // SELECCIONAR * FROM `usuario`` u`, `perfil`;
     * $ query = (new \ yii \ db \ Query) -> from (['u' => 'usuario', 'perfil']);
     *
     * // SELECCIONAR * FROM (SELECCIONAR * FROM `usuario` DONDE` activo` = 1) `activeusers`;
     * $ subquery = (nuevo \ yii \ db \ Query) -> from ('user') -> where (['active' => true])
     * $ query = (new \ yii \ db \ Query) -> from (['activeusers' => $ subquery]);
     *
     * // subquery también puede ser una cadena con SQL simple envuelto entre paréntesis
     * // SELECCIONAR * FROM (SELECCIONAR * FROM `usuario` DONDE` activo` = 1) `activeusers`;
     * $ subquery = "(SELECCIONAR * FROM` usuario` DONDE `activo` = 1)";
     * $ query = (new \ yii \ db \ Query) -> from (['activeusers' => $ subquery]);
     * `` `
     *
     * @return $ this el objeto de consulta en sí
     * /
     función  pública desde ( $ mesas )
    {
        if ( $ tables  instanceof  ExpressionInterface ) {
            $ tables  = [ $ tables ];
        }
        if ( is_string ( $ tables )) {
            $ tables  =  preg_split ( '/ \ s * , \ s * /' , trim ( $ tables ), - 1 , PREG_SPLIT_NO_EMPTY );
        }
        $ this -> from  =  $ tables ;
        devuelve  $ this ;
    }
    / **
     * Establece la parte WHERE de la consulta.
     *
     * El método requiere un parámetro `$ condition`, y opcionalmente un parámetro` $ params`
     * especificando los valores a vincular a la consulta.
     *
     * El parámetro `$ condition` debe ser una cadena (por ej.,`'id = 1'`) o una matriz.
     *
     * {@inheritdoc}
     *
     * @param string | array | ExpressionInterface $ condiciona las condiciones que se deben poner en la parte WHERE.
     * @param array $ params los parámetros (nombre => valor) para vincular a la consulta.
     * @return $ this el objeto de consulta en sí
     * @see andWhere ()
     * @see orWhere ()
     * @see QueryInterface :: where ()
     * /
     función  pública donde ( $ condición , $ params  = [])
    {
        $ this -> where  =  $ condition ;
        $ this -> addParams ( $ params );
        devuelve  $ this ;
    }
    / **
     * Agrega una condición WHERE adicional a la existente.
     * La nueva condición y la existente se unirán usando el operador `AND`.
     * @param string | array | ExpressionInterface $ condiciona la nueva condición WHERE. Por favor refiérase a [[donde ()]]
     * sobre cómo especificar este parámetro.
     * @param array $ params los parámetros (nombre => valor) para vincular a la consulta.
     * @return $ this el objeto de consulta en sí
     * @ver dónde ()
     * @see orWhere ()
     * /
     función  pública y donde ( $ condición , $ params  = [])
    {
        if ( $ this -> where  ===  null ) {
            $ this -> where  =  $ condition ;
        } elseif ( is_array ( $ this -> where ) &&  isset ( $ this -> donde [ 0 ]) &&  strcasecmp ( $ this -> donde [ 0 ], ' and ' ) ===  0 ) {
            $ this -> donde [] =  $ condición ;
        } else {
            $ this -> where  = [ ' and ' , $ this -> where , $ condition ];
        }
        $ this -> addParams ( $ params );
        devuelve  $ this ;
    }
    / **
     * Agrega una condición WHERE adicional a la existente.
     * La nueva condición y la existente se unirán usando el operador `OR`.
     * @param string | array | ExpressionInterface $ condiciona la nueva condición WHERE. Por favor refiérase a [[donde ()]]
     * sobre cómo especificar este parámetro.
     * @param array $ params los parámetros (nombre => valor) para vincular a la consulta.
     * @return $ this el objeto de consulta en sí
     * @ver dónde ()
     * @see andWhere ()
     * /
     función  pública orWhere ( $ condición , $ params  = [])
    {
        if ( $ this -> where  ===  null ) {
            $ this -> where  =  $ condition ;
        } else {
            $ this -> where  = [ ' or ' , $ this -> where , $ condition ];
        }
        $ this -> addParams ( $ params );
        devuelve  $ this ;
    }
    / **
     * Agrega una condición de filtrado para una columna específica y le permite al usuario elegir un operador de filtro.
     *
     * Agrega una condición WHERE adicional para el campo dado y determina el operador de comparación
     * basado en los primeros pocos caracteres del valor dado.
     * La condición se agrega de la misma manera que en [[andFilterWhere]] por lo que [[isEmpty () | empty values]] se ignoran.
     * La nueva condición y la existente se unirán usando el operador `AND`.
     *
     * El operador de comparación se determina de forma inteligente en función de los primeros caracteres en el valor dado.
     * En particular, reconoce los siguientes operadores si aparecen como los caracteres principales en el valor dado:
     *
     * - `<`: la columna debe ser menor que el valor dado.
     * - `>`: la columna debe ser mayor que el valor dado.
     * - `<=`: la columna debe ser menor o igual que el valor dado.
     * - `> =`: la columna debe ser mayor o igual que el valor dado.
     * - `<>`: la columna no debe ser igual que el valor dado.
     * - `=`: la columna debe ser igual al valor dado.
     * - Si no se detecta ninguno de los operadores anteriores, se usará el `$ defaultoperator`.
     *
     * @param string $ nombre el nombre de la columna.
     * @ param string $ valora el valor de columna opcionalmente antepuesto con el operador de comparación.
     * @ param string $ defaultOperator El operador a usar, cuando no se da ningún operador en `$ value`.
     * Predeterminado a `=`, realizando una coincidencia exacta.
     * @return $ this El objeto de consulta en sí
     * @since 2.0.8
     * /
     función  pública y filtro de comparación ( $ name , $ value , $ defaultOperator  =  ' = ' )
    {
        if ( preg_match ( '/ ^ (<> |> = |> | <= | <| =) /' , $ valor , $ coincidencias )) {
            $ operator  =  $ coincidencias [ 1 ];
            $ value  =  substr ( $ value , strlen ( $ operator ));
        } else {
            $ operator  =  $ defaultOperator ;
        }
        devuelve  $ this -> andFilterWhere ([ $ operator , $ name , $ value ]);
    }
    / **
     * Añade una parte JOIN a la consulta.
     * El primer parámetro especifica qué tipo de unión es.
     * @param string $ escriba el tipo de unión, como INNER JOIN, LEFT JOIN.
     * @param string | array $ table la tabla a unir.
     *
     * Use una cadena para representar el nombre de la tabla a unir.
     * El nombre de la tabla puede contener un prefijo de esquema (por ejemplo, 'public.user') y / o alias de tabla (por ejemplo, 'usuario u').
     * El método citará automáticamente el nombre de la tabla a menos que contenga algunos paréntesis
     * (lo que significa que la tabla se da como una subconsulta o expresión DB).
     *
     * Use una matriz para representar la unión con una subconsulta. La matriz debe contener solo un elemento.
     * El valor debe ser un objeto [[Query]] que represente la subconsulta mientras la clave correspondiente
     * representa el alias para la subconsulta.
     *
     * @ param string | array $ en la condición de unión que debería aparecer en la parte ON.
     * Consulte [[where ()]] sobre cómo especificar este parámetro.
     *
     * Tenga en cuenta que el formato de matriz de [[donde ()]] está diseñado para unir columnas a valores en lugar de columnas a columnas, por lo que
     * lo siguiente ** ** funcionaría como se esperaba: `['post.author_id' => 'user.id']`, lo haría
     * coincide con el valor de la columna `post.author_id` contra la cadena` 'user.id'`.
     * Se recomienda utilizar aquí la sintaxis de cadena que es más adecuada para una unión:
     *
     * `` `php
     * 'post.author_id = user.id'
     * `` `
     *
     * @param array $ params los parámetros (nombre => valor) para vincular a la consulta.
     * @return $ this el objeto de consulta en sí
     * /
     combinación de función  pública ( $ tipo , $ tabla , $ on = ' ' , $ params = [])   
    {
        $ this -> join [] = [ $ type , $ table , $ on ];
        devuelve  $ this -> addParams ( $ params );
    }
    / **
     * Añade una parte INNER JOIN a la consulta.
     * @param string | array $ table la tabla a unir.
     *
     * Use una cadena para representar el nombre de la tabla a unir.
     * El nombre de la tabla puede contener un prefijo de esquema (por ejemplo, 'public.user') y / o alias de tabla (por ejemplo, 'usuario u').
     * El método citará automáticamente el nombre de la tabla a menos que contenga algunos paréntesis
     * (lo que significa que la tabla se da como una subconsulta o expresión DB).
     *
     * Use una matriz para representar la unión con una subconsulta. La matriz debe contener solo un elemento.
     * El valor debe ser un objeto [[Query]] que represente la subconsulta mientras la clave correspondiente
     * representa el alias para la subconsulta.
     *
     * @ param string | array $ en la condición de unión que debería aparecer en la parte ON.
     * Consulte [[join ()]] sobre cómo especificar este parámetro.
     * @param array $ params los parámetros (nombre => valor) para vincular a la consulta.
     * @return $ this el objeto de consulta en sí
     * /
     función  pública innerJoin ( $ table , $ on  =  ' ' , $ params  = [])
    {
        $ this -> join [] = [ ' INNER JOIN ' , $ table , $ on ];
        devuelve  $ this -> addParams ( $ params );
    }
    / **
     * Añade una parte IZQUIERDA DE UNIÓN EXTERIOR a la consulta.
     * @param string | array $ table la tabla a unir.
     *
     * Use una cadena para representar el nombre de la tabla a unir.
     * El nombre de la tabla puede contener un prefijo de esquema (por ejemplo, 'public.user') y / o alias de tabla (por ejemplo, 'usuario u').
     * El método citará automáticamente el nombre de la tabla a menos que contenga algunos paréntesis
     * (lo que significa que la tabla se da como una subconsulta o expresión DB).
     *
     * Use una matriz para representar la unión con una subconsulta. La matriz debe contener solo un elemento.
     * El valor debe ser un objeto [[Query]] que represente la subconsulta mientras la clave correspondiente
     * representa el alias para la subconsulta.
     *
     * @ param string | array $ en la condición de unión que debería aparecer en la parte ON.
     * Consulte [[join ()]] sobre cómo especificar este parámetro.
     * @param array $ params los parámetros (nombre => valor) a vincular a la consulta
     * @return $ this el objeto de consulta en sí
     * /
     función  pública leftJoin ( $ table , $ on  =  ' ' , $ params  = [])
    {
        $ this -> join [] = [ ' LEFT JOIN ' , $ table , $ on ];
        devuelve  $ this -> addParams ( $ params );
    }
    / **
     * Añade una parte DERECHA EXTERIOR DE UNIÓN a la consulta.
     * @param string | array $ table la tabla a unir.
     *
     * Use una cadena para representar el nombre de la tabla a unir.
     * El nombre de la tabla puede contener un prefijo de esquema (por ejemplo, 'public.user') y / o alias de tabla (por ejemplo, 'usuario u').
     * El método citará automáticamente el nombre de la tabla a menos que contenga algunos paréntesis
     * (lo que significa que la tabla se da como una subconsulta o expresión DB).
     *
     * Use una matriz para representar la unión con una subconsulta. La matriz debe contener solo un elemento.
     * El valor debe ser un objeto [[Query]] que represente la subconsulta mientras la clave correspondiente
     * representa el alias para la subconsulta.
     *
     * @ param string | array $ en la condición de unión que debería aparecer en la parte ON.
     * Consulte [[join ()]] sobre cómo especificar este parámetro.
     * @param array $ params los parámetros (nombre => valor) a vincular a la consulta
     * @return $ this el objeto de consulta en sí
     * /
     función  pública rightJoin ( $ table , $ on  =  ' ' , $ params  = [])
    {
        $ this -> join [] = [ ' RIGHT JOIN ' , $ table , $ on ];
        devuelve  $ this -> addParams ( $ params );
    }
    / **
     * Establece la parte GROUP BY de la consulta.
     * @param string | array | ExpressionInterface $ columns las columnas por agrupar.
     * Las columnas se pueden especificar en una cadena (por ejemplo, "id, name") o en una matriz (por ejemplo, ['id', 'name']).
     * El método citará automáticamente los nombres de las columnas a menos que una columna contenga algunos paréntesis
     * (lo que significa que la columna contiene una expresión DB).
     *
     * Tenga en cuenta que si su group-by es una expresión que contiene comas, siempre debe usar una matriz
     * para representar la información de grupo. De lo contrario, el método no podrá determinar correctamente
     * las columnas group-by.
     *
     * Desde la versión 2.0.7, se puede pasar un objeto [[ExpressionInterface]] para especificar la parte GROUP BY explícitamente en SQL puro.
     * Desde la versión 2.0.14, también se puede pasar un objeto [[ExpressionInterface]].
     * @return $ this el objeto de consulta en sí
     * @see addGroupBy ()
     * /
    public  function  groupBy ( $ columns )
    {
        if ( $ columns  instanceof  ExpressionInterface ) {
            $ columns  = [ $ columns ];
        } elseif ( ! is_array ( $ columns )) {
            $ columns  =  preg_split ( '/ \ s * , \ s * /' , trim ( $ columns ), - 1 , PREG_SPLIT_NO_EMPTY );
        }
        $ this -> groupBy  =  $ columns ;
        devuelve  $ this ;
    }
    / **
     * Agrega columnas adicionales por grupos a las existentes.
     * @param string | array $ columnas columnas adicionales para agrupar.
     * Las columnas se pueden especificar en una cadena (por ejemplo, "id, name") o en una matriz (por ejemplo, ['id', 'name']).
     * El método citará automáticamente los nombres de las columnas a menos que una columna contenga algunos paréntesis
     * (lo que significa que la columna contiene una expresión DB).
     *
     * Tenga en cuenta que si su group-by es una expresión que contiene comas, siempre debe usar una matriz
     * para representar la información de grupo. De lo contrario, el método no podrá determinar correctamente
     * las columnas group-by.
     *
     * Desde la versión 2.0.7, se puede pasar un objeto [[Expression]] para especificar la parte GROUP BY explícitamente en SQL simple.
     * Desde la versión 2.0.14, también se puede pasar un objeto [[ExpressionInterface]].
     * @return $ this el objeto de consulta en sí
     * @see groupBy ()
     * /
     función  pública addGroupBy ( $ columns )
    {
        if ( $ columns  instanceof  ExpressionInterface ) {
            $ columns  = [ $ columns ];
        } elseif ( ! is_array ( $ columns )) {
            $ columns  =  preg_split ( '/ \ s * , \ s * /' , trim ( $ columns ), - 1 , PREG_SPLIT_NO_EMPTY );
        }
        if ( $ this -> groupBy  ===  null ) {
            $ this -> groupBy  =  $ columns ;
        } else {
            $ this -> groupBy  =  array_merge ( $ this -> groupBy , $ columns );
        }
        devuelve  $ this ;
    }
    / **
     * Establece la parte HAVING de la consulta.
     * @param string | array | ExpressionInterface $ condiciona las condiciones que se colocarán después de HAVING.
     * Consulte [[where ()]] sobre cómo especificar este parámetro.
     * @param array $ params los parámetros (nombre => valor) para vincular a la consulta.
     * @return $ this el objeto de consulta en sí
     * @see andHaving ()
     * @see orHaving ()
     * /
     función  pública que tiene ( $ condition , $ params  = [])
    {
        $ this -> having  =  $ condition ;
        $ this -> addParams ( $ params );
        devuelve  $ this ;
    }
    / **
     * Agrega una condición HAVING adicional a la existente.
     * La nueva condición y la existente se unirán usando el operador `AND`.
     * @param string | array | ExpressionInterface $ condiciona la nueva condición HAVING. Por favor refiérase a [[donde ()]]
     * sobre cómo especificar este parámetro.
     * @param array $ params los parámetros (nombre => valor) para vincular a la consulta.
     * @return $ this el objeto de consulta en sí
     * @see tener ()
     * @see orHaving ()
     * /
     función  pública y Habilidad ( $ condición , $ params  = [])
    {
        if ( $ this -> having  ===  null ) {
            $ this -> having  =  $ condition ;
        } else {
            $ this -> having  = [ ' and ' , $ this -> having , $ condition ];
        }
        $ this -> addParams ( $ params );
        devuelve  $ this ;
    }
    / **
     * Agrega una condición HAVING adicional a la existente.
     * La nueva condición y la existente se unirán usando el operador `OR`.
     * @param string | array | ExpressionInterface $ condiciona la nueva condición HAVING. Por favor refiérase a [[donde ()]]
     * sobre cómo especificar este parámetro.
     * @param array $ params los parámetros (nombre => valor) para vincular a la consulta.
     * @return $ this el objeto de consulta en sí
     * @see tener ()
     * @see andHaving ()
     * /
     función  pública o Habilidad ( $ condición , $ params  = [])
    {
        if ( $ this -> having  ===  null ) {
            $ this -> having  =  $ condition ;
        } else {
            $ this -> having  = [ ' or ' , $ this -> having , $ condition ];
        }
        $ this -> addParams ( $ params );
        devuelve  $ this ;
    }
    / **
     * Establece la parte HAVING de la consulta pero ignora [[isEmpty () | operandos vacíos]].
     *
     * Este método es similar a [[having ()]]. La principal diferencia es que este método
     * eliminar [[isEmpty () | operandos de consulta vacíos]]. Como resultado, este método es el más adecuado
     * para crear condiciones de consulta basadas en valores de filtro ingresados ​​por los usuarios.
     *
     * El siguiente código muestra la diferencia entre este método y [[having ()]]:
     *
     * `` `php
     * // TENIENDO `edad` =: edad
     * $ query-> filterHaving (['name' => null, 'age' => 20]);
     * // TENIENDO `edad` =: edad
     * $ query-> having (['age' => 20]);
     * // TENER `name` ES NULL Y` age` =: age
     * $ query-> having (['name' => null, 'age' => 20]);
     * `` `
     *
     * Tenga en cuenta que a diferencia de [[having ()]], no puede pasar parámetros de enlace a este método.
     *
     * @param array $ condiciona las condiciones que se deben poner en la parte HAVING.
     * Ver [[having ()]] sobre cómo especificar este parámetro.
     * @return $ this el objeto de consulta en sí
     * @see tener ()
     * @see andFilterHaving ()
     * @see orFilterHaving ()
     * @ desde 2.0.11
     * /
     función  pública filterHaving ( array  $ condition )
    {
        $ condition  =  $ this -> filterCondition ( $ condition );
        if ( $ condition  ! == []) {
            $ this -> having ( $ condition );
        }
        devuelve  $ this ;
    }
    / **
     * Agrega una condición HAVING adicional a la existente pero ignora [[isEmpty () | operandos vacíos]].
     * La nueva condición y la existente se unirán usando el operador `AND`.
     *
     * Este método es similar a [[andHaving ()]]. La principal diferencia es que este método
     * eliminar [[isEmpty () | operandos de consulta vacíos]]. Como resultado, este método es el más adecuado
     * para crear condiciones de consulta basadas en valores de filtro ingresados ​​por los usuarios.
     *
     * @param array $ condiciona la nueva condición HAVING. Por favor refiérase a [[having ()]]
     * sobre cómo especificar este parámetro.
     * @return $ this el objeto de consulta en sí
     * @see filterHaving ()
     * @see orFilterHaving ()
     * @ desde 2.0.11
     * /
     función  pública y FiltrarHaving ( condición $ array  )
    {
        $ condition  =  $ this -> filterCondition ( $ condition );
        if ( $ condition  ! == []) {
            $ this -> andHaving ( $ condición );
        }
        devuelve  $ this ;
    }
    / **
     * Agrega una condición HAVING adicional a la existente pero ignora [[isEmpty () | operandos vacíos]].
     * La nueva condición y la existente se unirán usando el operador `OR`.
     *
     * Este método es similar a [[oHaving ()]]. La principal diferencia es que este método
     * eliminar [[isEmpty () | operandos de consulta vacíos]]. Como resultado, este método es el más adecuado
     * para crear condiciones de consulta basadas en valores de filtro ingresados ​​por los usuarios.
     *
     * @param array $ condiciona la nueva condición HAVING. Por favor refiérase a [[having ()]]
     * sobre cómo especificar este parámetro.
     * @return $ this el objeto de consulta en sí
     * @see filterHaving ()
     * @see andFilterHaving ()
     * @ desde 2.0.11
     * /
     función  pública o FiltrarHaving ( condición $ array  )
    {
        $ condition  =  $ this -> filterCondition ( $ condition );
        if ( $ condition  ! == []) {
            $ this -> orHaving ( $ condición );
        }
        devuelve  $ this ;
    }
    / **
     * Añade una declaración de SQL utilizando el operador de UNIÓN.
     * @ param string | Query $ sql la instrucción SQL que se agregará utilizando UNION
     * @param bool $ all TRUE si usa UNION ALL y FALSE si usa UNION
     * @return $ this el objeto de consulta en sí
     * /
     unión de función  pública ( $ sql , $ all = false )  
    {
        $ this -> union [] = [ ' query '  =>  $ sql , ' all '  =>  $ all ];
        devuelve  $ this ;
    }
    / **
     * Establece los parámetros a vincular a la consulta.
     * @param array $ params lista de valores de parámetros de consulta indexados por marcadores de posición de parámetros.
     * Por ejemplo, `[': name' => 'Dan', ': age' => 31]`.
     * @return $ this el objeto de consulta en sí
     * @see addParams ()
     * /
     params de funciones  públicas ( $ params )
    {
        $ this -> params  =  $ params ;
        devuelve  $ this ;
    }
    / **
     * Agrega parámetros adicionales para vincularse a la consulta.
     * @param array $ params lista de valores de parámetros de consulta indexados por marcadores de posición de parámetros.
     * Por ejemplo, `[': name' => 'Dan', ': age' => 31]`.
     * @return $ this el objeto de consulta en sí
     * @see params ()
     * /
     función  pública addParams ( $ params )
    {
        if ( ! empty ( $ params )) {
            if ( empty ( $ this -> params )) {
                $ this -> params  =  $ params ;
            } else {
                foreach ( $ params  as  $ name  =>  $ value ) {
                    if ( is_int ( $ nombre )) {
                        $ this -> params [] =  $ value ;
                    } else {
                        $ this -> params [ $ name ] =  $ value ;
                    }
                }
            }
        }
        devuelve  $ this ;
    }
    / **
     * Habilita el caché de consultas para esta consulta.
     * @param int | true $ duration la cantidad de segundos que los resultados de la consulta pueden permanecer válidos en la memoria caché.
     * Use 0 para indicar que los datos en caché nunca caducarán.
     * Use un número negativo para indicar que la caché de consultas no debe ser utilizada.
     * Use boolean `true` para indicar que se debe usar [[Connection :: queryCacheDuration]].
     * Predeterminado a 'verdadero'.
     * @param \ yii \ caching \ Dependency $ dependency la dependencia de caché asociada con el resultado en caché.
     * @return $ este es el objeto Query
     * @since 2.0.14
     * /
     caché de función  pública ( $ duration = true , $ dependency = null )    
    {
        $ this -> queryCacheDuration  =  $ duration ;
        $ this -> queryCacheDependency  =  $ dependency ;
        devuelve  $ this ;
    }
    / **
     * Desactiva el caché de consultas para esta consulta.
     * @return $ este es el objeto Query
     * @since 2.0.14
     * /
     función  pública noCache ()
    {
        $ this -> queryCacheDuration  =  - 1 ;
        devuelve  $ this ;
    }
    / **
     * Establece $ command cache, si esta consulta ha habilitado el almacenamiento en caché.
     *
     * Comando @param Command $
     * @return Command
     * @since 2.0.14
     * /
     función  protegida setCommandCache ( $ command )
    {
        if ( $ this -> queryCacheDuration  ! ==  null  ||  $ this -> queryCacheDependency  ! ==  null ) {
            $ duration  =  $ this -> queryCacheDuration  ===  true ? null : $ this -> queryCacheDuration ;
            $ command -> cache ( $ duration , $ this -> queryCacheDependency );
        }
        return  $ command ;
    }
    / **
     * Crea un nuevo objeto Query y copia sus valores de propiedad de uno existente.
     * Las propiedades que se copian son las que usarán los creadores de consultas.
     * @param Query $ del objeto de consulta de origen
     * @return Consulta el nuevo objeto Query
     * /
     función estática  pública create ( $ from ) 
    {
        devolver el  nuevo  yo ([
            ' where '  =>  $ from -> where ,
            ' límite '  =>  $ desde -> límite ,
            ' offset '  =>  $ from -> offset ,
            ' orderBy '  =>  $ from -> orderBy ,
            ' indexBy '  =>  $ desde -> indexBy ,
            ' seleccionar '  =>  $ de -> seleccionar ,
            ' selectOption '  =>  $ desde -> selectOption ,
            ' distinct '  =>  $ from -> distinct ,
            ' from '  =>  $ from -> from ,
            ' groupBy '  =>  $ desde -> groupBy ,
            ' join '  =>  $ from -> join ,
            ' having '  =>  $ from -> teniendo ,
            ' union '  =>  $ desde -> union ,
            ' params '  =>  $ desde -> params ,
        ]);
    }
    / **
     * Devuelve la representación SQL de Query
     * @return string
     * /
     función  pública __toString ()
    {
        devolver  serializar ( $ this );
    }
}