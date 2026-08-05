<?php
foreach (config('permissions.modules.chatbox.sub_modules') as $key => $val) {
    App\Models\Permission::firstOrCreate(['name' => $key]);
}
echo "done";
