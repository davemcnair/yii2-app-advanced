#!/bin/bash
cd $(dirname $0)
if [ ! -f ../backend/config/main-local.template.php ]; then     cp ../backend/config/main-local.php ../backend/config/main-local.template.php  ; fi
if [ ! -f ../backend/config/params-local.template.php ]; then     cp ../backend/config/params-local.php ../backend/config/params-local.template.php ; fi
if [ ! -f ../backend/config/test-local.template.php ]; then     cp ../backend/config/test-local.php ../backend/config/test-local.template.php ; fi
if [ ! -f ../common/config/main-local.template.php ]; then     cp ../common/config/main-local.php ../common/config/main-local.template.php ; fi
if [ ! -f ../common/config/params-local.template.php ]; then     cp ../common/config/params-local.php ../common/config/params-local.template.php ; fi
if [ ! -f ../common/config/test-local.template.php ]; then     cp ../common/config/test-local.php ../common/config/test-local.template.php ; fi
if [ ! -f ../console/config/main-local.template.php ]; then     cp ../console/config/main-local.php ../console/config/main-local.template.php ; fi
if [ ! -f ../console/config/params-local.template.php ]; then     cp ../console/config/params-local.php ../console/config/params-local.template.php ; fi
if [ ! -f ../frontend/config/main-local.template.php ]; then     cp ../frontend/config/main-local.php ../frontend/config/main-local.template.php ; fi
if [ ! -f ../frontend/config/params-local.template.php ]; then     cp ../frontend/config/params-local.php ../frontend/config/params-local.template.php ; fi
if [ ! -f ../frontend/config/test-local.template.php ]; then     cp ../frontend/config/test-local.php ../frontend/config/test-local.template.php ; fi

