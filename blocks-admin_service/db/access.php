<?php
$block_admin_service_capabilities = array(
    'block/admin_service:viewblock' => array(
   //     'riskbitmask' => RISK_SPAM | RISK_PERSONAL | RISK_XSS | RISK_CONFIG,
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'legacy' => array(
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),
    'block/admin_service::admin' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'legacy' => array(
			'admin' => CAP_ALLOW
        )
    ),
    'block/admin_service::gestor' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'legacy' => array(
			'admin' => CAP_ALLOW
        )
    ),
    'block/admin_service::user' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'legacy' => array(
			'admin' => CAP_ALLOW
        )
    )
)
?>