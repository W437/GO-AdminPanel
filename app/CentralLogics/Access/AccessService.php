<?php

namespace App\CentralLogics\Access;

class AccessService
{
    public static function module_permission_check($mod_name)
    {
        if (!auth('admin')->user()->role) {
            return false;
        }

        if ($mod_name == 'zone' && auth('admin')->user()->zone_id) {
            return false;
        }

        $permission = auth('admin')->user()->role->modules;
        if (isset($permission) && in_array($mod_name, (array)json_decode($permission)) == true) {
            return true;
        }

        if (auth('admin')->user()->role_id == 1) {
            return true;
        }
        return false;
    }

    public static function employee_module_permission_check($mod_name)
    {
        if (auth('vendor')->check()) {
            if ($mod_name == 'reviews') {
                return auth('vendor')->user()->restaurants[0]->reviews_section;
            } else if ($mod_name == 'deliveryman') {
                return auth('vendor')->user()->restaurants[0]->self_delivery_system;
            } else if ($mod_name == 'pos') {
                return auth('vendor')->user()->restaurants[0]->pos_system;
            }
            return true;
        } else if (auth('vendor_employee')->check()) {
            $permission = auth('vendor_employee')->user()->role->modules;
            if (isset($permission) && in_array($mod_name, (array)json_decode($permission)) == true) {
                if ($mod_name == 'reviews') {
                    return auth('vendor_employee')->user()->restaurant->reviews_section;
                } else if ($mod_name == 'deliveryman') {
                    return auth('vendor_employee')->user()->restaurant->self_delivery_system;
                } else if ($mod_name == 'pos') {
                    return auth('vendor_employee')->user()->restaurant->pos_system;
                }
                return true;
            }
        }
        return false;
    }

    public static function get_restaurant_id()
    {
        if (auth('vendor_employee')->check()) {
            return auth('vendor_employee')->user()->restaurant->id;
        }
        return auth('vendor')->user()->restaurants[0]->id;
    }

    public static function get_vendor_id()
    {
        if (auth('vendor')->check()) {
            return auth('vendor')->id();
        } else if (auth('vendor_employee')->check()) {
            return auth('vendor_employee')->user()->vendor_id;
        }
        return 0;
    }

    public static function get_vendor_data()
    {
        if (auth('vendor')->check()) {
            return auth('vendor')->user();
        } else if (auth('vendor_employee')->check()) {
            return auth('vendor_employee')->user()->vendor;
        }
        return 0;
    }

    public static function get_loggedin_user()
    {
        if (auth('vendor')->check()) {
            return auth('vendor')->user();
        } else if (auth('vendor_employee')->check()) {
            return auth('vendor_employee')->user();
        }
        return 0;
    }

    public static function get_restaurant_data()
    {
        if (auth('vendor_employee')->check()) {
            return auth('vendor_employee')->user()->restaurant;
        }
        return auth('vendor')->user()->restaurants[0];
    }
}
