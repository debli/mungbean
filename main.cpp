#include <phpcpp.h>
#include "config.h"

class Route: public Php::Base
{
    private:
        int _val = 0;

    public:
        Route() {}
        virtual ~Route() {}
        
        Php::Value inc(Php::Parameters &params)
        { 
            return _val += params.size() > 0 ? (int) params[0] : 1;
        }

        Php::Value dec(Php::Parameters &params)
        { 
            return _val -= params.size() > 0 ? (int) params[0] : 1;
        }

        Php::Value getVal()
        {
            return _val;
        }
};

/**
 *  tell the compiler that the get_module is a pure C function
 */
extern "C" {
    
    /**
     *  Function that is called by PHP right after the PHP process
     *  has started, and that returns an address of an internal PHP
     *  strucure with all the details and features of your extension
     *
     *  @return void*   a pointer to an address that is understood by PHP
     */
    PHPCPP_EXPORT void *get_module() 
    {
        // static(!) Php::Extension object that should stay in memory
        // for the entire duration of the process (that's why it's static)
        static Php::Extension extension("mung", "1.0");
        Php::Class<Route> route("Route");
        
        route.method("inc", &Route::inc, { 
            Php::ByVal("change", Php::Type::Numeric, false) 
        });
        
        // register the decrement, and specify its parameters
        route.method("dec", &Route::dec, { 
            Php::ByVal("change", Php::Type::Numeric, false) 
        });
        
        // register the value method
        route.method("getVal", &Route::getVal, {});
       // Php::Namespace myNamespace("Route");
        Php::Class<Config> config("Config");
        
        config.method("inc", &Config::inc, { 
            Php::ByVal("change", Php::Type::Numeric, false) 
        });
        
        // register the decrement, and specify its parameters
        config.method("dec", &Config::dec, { 
            Php::ByVal("change", Php::Type::Numeric, false) 
        });
        
        // register the value method
        config.method("getVal", &Config::getVal, {});
       // Php::Namespace myNamespace("config");
        
        // add the class to the extension
        extension.add(std::move(route));
        extension.add(std::move(config));
        
        // @todo    add your own functions, classes, namespaces to the extension
        
        // return the extension
        return extension;
    }
}
