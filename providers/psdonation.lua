--[[
    PointShop tMySQL provider by Jackson Cannon
    For use with PointShop-Donation: https://github.com/jackson-c/PointShop-Donation

    Adapted from the tMySQL provider by Spencer Sharkey

    tmysql4 and libmysql dlls must be installed for this to work.
    
    Be sure to setup and connect to the donation site once before using this, to set up the data tables.
    Once configured, change PS.Config.DataProvider = 'pdata' to PS.Config.DataProvider = 'psdonation' in pointshop's sh_config.lua.
]]--

-- config

local mysql_hostname = '127.0.0.1' -- Your MySQL server address.
local mysql_username = 'username' -- Your MySQL username.
local mysql_password = 'password' -- Your MySQL password.
local mysql_database = 'mydatabase' -- Your MySQL database.
local mysql_port = 3306 -- Your MySQL port. Most likely is 3306.

-- see below for setting up donator ranks

require('tmysql4')

PROVIDER.Fallback = "pdata"

local db, err = tmysql.initialize(mysql_hostname, mysql_username, mysql_password, mysql_database, mysql_port)

if (err) then
    print("Error connecting to MySQL:")
    ErrorNoHalt(err)
else
    function PROVIDER:GetData(ply, callback)

        qs = string.format("SELECT * FROM `users` WHERE id64='%s'", ply:SteamID64())
        db:Query(qs, function(res)
            res=res[1]
            if (not res.status) then ErrorNoHalt("[PSMySQL-GetData] "..res.error) return end
            res=res.data
            if (#res < 1) then callback(0, {}) return end
            local row = res[1]
            ply.donatedAmount = row.donation_total or 0
            --[[

            --Example setup of donator rank:
            --if player has donated $5 or more, set them to donator rank, otherwise set them to user rank
            --but we don't want to override admin ranks

            if !ply:IsAdmin() then
                if ply.donatedAmount >= 500 then

                    ply:SetUserGroup("donator")

                else

                    ply:SetUserGroup("user")

                end
            end

            ]]--
            print("Pointshop loaded: "..row.points.." points for "..ply:Nick())
            callback(row.points or 0, util.JSONToTable(row.items or '{}'))
        end, QUERY_FLAG_ASSOC)
    end

    function PROVIDER:SetPoints(ply, points)
        local qs = string.format("INSERT INTO `users` (id64, points, items) VALUES ('%s', '%s', '[]') ON DUPLICATE KEY UPDATE points = VALUES(points)", ply:SteamID64(), points or 0)
        db:Query(qs, function(res)
            res=res[1]
            if (not res.status) then ErrorNoHalt("[PSMySQL-SetPoints] "..res.error) return end
        end, QUERY_FLAG_ASSOC)
    end

    function PROVIDER:GivePoints(ply, points)
        local qs = string.format("INSERT INTO `users` (id64, points, items) VALUES ('%s', '%s', '[]') ON DUPLICATE KEY UPDATE points = points + VALUES(points)", ply:SteamID64(), points or 0)
        db:Query(qs, function(res, pass, err)
            res=res[1]
            if (not res.status) then ErrorNoHalt("[PSMySQL-GivePoints] "..res.error) return end
        end, QUERY_FLAG_ASSOC)
    end

    function PROVIDER:TakePoints(ply, points)
        local qs = string.format("INSERT INTO `users` (id64, points, items) VALUES ('%s', '%s', '[]') ON DUPLICATE KEY UPDATE points = points - VALUES(points)", ply:SteamID64(), points or 0)
        db:Query(qs, function(res, pass, err)
            res=res[1]
            if (not res.status) then ErrorNoHalt("[PSMySQL-TakePoints] "..res.error) return end
        end, QUERY_FLAG_ASSOC)
    end

    function PROVIDER:SaveItem(ply, item_id, data)
        self:GiveItem(ply, item_id, data)
    end

    function PROVIDER:GiveItem(ply, item_id, data)
        local tmp = table.Copy(ply.PS_Items)
        tmp[item_id] = data
        local qs = string.format("INSERT INTO `users` (id64, points, items) VALUES ('%s', '0', '%s') ON DUPLICATE KEY UPDATE items = VALUES(items)", ply:SteamID64(), db:Escape(util.TableToJSON(tmp)))
        db:Query(qs, function(res, pass, err)
            res=res[1]
            if (not res.status) then ErrorNoHalt("[PSMySQL-GiveItem] "..res.error) return end
        end, QUERY_FLAG_ASSOC)
    end

    function PROVIDER:TakeItem(ply, item_id)
        local tmp = table.Copy(ply.PS_Items)
        tmp[item_id] = nil
        local qs = string.format("INSERT INTO `users` (id64, points, items) VALUES ('%s', '0', '%s') ON DUPLICATE KEY UPDATE items = VALUES(items)", ply:SteamID64(), db:Escape(util.TableToJSON(tmp)))
        db:Query(qs, function(res, pass, err)
            res=res[1]
            if (not res.status) then ErrorNoHalt("[PSMySQL-TakeItem] "..res.error) return end
        end, QUERY_FLAG_ASSOC)
    end

    function PROVIDER:SetData(ply, points, items)
        local qs = string.format("INSERT INTO `users` (id64, points, items) VALUES ('%s', '%s', '%s') ON DUPLICATE KEY UPDATE points = VALUES(points), items = VALUES(items)", ply:SteamID64(), points or 0, db:Escape(util.TableToJSON(items)))
        db:Query(qs, function(res, pass, err)
            res=res[1]
            if (not res.status) then ErrorNoHalt("[PSMySQL-SetData] "..res.error) return end
        end, QUERY_FLAG_ASSOC)
    end
end