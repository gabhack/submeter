import React from 'react';

import ClockLoader from "react-spinners/ClockLoader";
import { css } from "@emotion/core";

const override = css`
  display: block;
  margin: 0 auto;
`;


const Loading = (props) => {
    if(props.show)
    {
        return (
            <div style={
                {
                    height: "100%",
                    position: "absolute",
                    width: "100%",
                    display: "flex",
                    left: "0",
                    top: "0",
                    opacity: "0.8",
                    background: "#e6e6e6",
                    zIndex: "999",
                    paddingTop: "30%"
                }
            }>
                <ClockLoader
                    css={override}
                    size={80}
                    color={"#123abc"}/>
            </div>
        );
    }
    
    return (
        <span></span>
    );
   
};

export default Loading;
