# PHP开发之数据验证

**1. 环境准备: 让错误都显示出来**

    # install xdebug (出错提示更醒目,更清楚)
    # php.ini
    error_reporting = E_ALL | E_STRICT (php 5.3.10)
    error_reporting = E_ALL (php 7.0.4)
    display_errors = On
    display_startup_errors = On
    log_errors = On
    track_errors = On
    html_errors = On (apache2)
    error_log = /var/log/php/apache2.log (apache2)
    error_log = /var/log/php/cli.log (cli)

**2. 确保所需数据用户都提交了**

    // 例1
    $id = $_GET['id'];
    $db->query("DELETE FROM table WHERE id = $id");
    // 例2
    $db->insert('table', array(
        'to'        => $_POST['to'],
        'subject'   => $_POST['subject'],
        'body'      => $_POST['body'],
        'sender_id' => $_SESSION['uid']
    ));

不验证的后果:

1. Notice: Undefined index
2. SQL Error / SQL Injection
3. 无效的/无意义的/垃圾数据插入了数据库表

验证方法:

    // 例1
    if (!isset($_GET['id'])) {
        throw new InvalidArgumentException('missing required key id');
    }
    // 例2
    $requiredKeys = array('title', 'content');
    foreach ($requiredKeys as $key) {
        if (!isset($_POST[$key])) {
            throw new InvalidArgumentException("missing required key $key");
        }
    }

**3. GET/POST数据分析**

1. $_GET/$_POST里的数据都是字符串(也会是数组,但数组里的数据最终还是字符串).
2. 可分为四类: 指定范围内的字符串,有约定格式的字符串,数字,任意文本

**4. 指定范围内的字符串: 白名单验证**

    // 例1: 用户信息完善之性别验证 (已确保 gender 字段已提交)

    // Bad
    $db->update('user', array('gender' => $_POST['gender']), "uid = $uid");

    // Good
    if ($_POST['gender'] != 'male' && $_POST['gender'] != 'female') {
        throw new InvalidArgumentException('invalid gender');
    }
    $db->update('user', array('gender' => $_POST['gender']), "uid = $uid");

    // 例2: 用户列表按状态显示 (已确保 status 字段已提交)

    // Bad
    $db->select()->from('user', '*')->where('status = ' . $_GET['status']);

    // Good
    class User {
        const STATUS_NEW        = 1;
        const STATUS_VERIFIED   = 2;
        const STATUS_EXPIRED    = 3;
        const STATUS_ARCHIVED   = 4;
    }
    $statusList = array(
        'all'                   => true,
        User::STATUS_NEW        => true,
        User::STATUS_VERIFIED   => true,
        User::STATUS_EXPIRED    => true,
        User::STATUS_ARCHIVED   => true
    );
    $status = $_GET['status'];
    if (!$status || !isset($statusList[$status])) {
        $status = 'all';
    }
    $select = $db->select()->from('user', '*');
    if ($status != 'all') {
        $select->where("status = $status");
    }

    // Why Not in_array() ???
    var_dump(in_array('1 any chars', $statusList)) === true // SQL injection here !!!

**5. 有约定格式的字符串: 长度 + 正则**

    // 例1: email
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        throew new InvalidArgumentException('invalid email');
    }

    // 例2: id list (3,4,5,6,7)
    $ids = $_GET['ids'];
    $len = strlen($ids);
    if ($len == 0 || $len > 1000) {
        throw new InvalidArgumentException('ids required and maxlength is 1000');
    }
    if (!preg_match('/^\d+(,\d+)*$/', $ids)) {
        throw new InvalidArgumentException('invalid ids');
    }

**6. 数字**

    // 例: 数据库表主键自增id
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
    if (!$id) {
        throw new InvalidArgumentException('invalid id');
    }

    // Why not is_numeric() ?
    var_dump(is_numeric('1e360')) === true // SQL Error here !!!

**7. 任意文本: 长度**

    // 例: subject VARCHAR(500), body (TEXT)
    $subject = trim($_POST['subject']);
    $len     = mb_strlen($subject, 'UTF-8');
    if ($len == 0 || $len > 500) {
        throw new InvalidArgumentException('subject required and maxlength is 500');
    }

    $body = trim($_POST['body']);
    $len  = mb_strlen($body, 'UTF-8');
    if ($len == 0 || $len > 65535) {
        throw new InvalidArgumentException('body required and maxlength is 65535');
    }

    // Why check length ?
    If strict SQL mode is not enabled and you assign a value to a CHAR or VARCHAR column that exceeds the column's maximum length, the value is **truncated** to fit and a warning is generated.
