function mkShowControlIfhasValues( setting, expectedValues  ) {

    return function( control ) {

    	//	Check the current value in the array of expectedValues
        var isDisplayed = function() {
            return jQuery.inArray( setting.get(), expectedValues ) !== -1;
        };

        var setActiveState = function() {
            control.active.set( isDisplayed() );
        };

        control.active.validate = isDisplayed;
        setActiveState();
        setting.bind( setActiveState );

    };

}
